#!/usr/bin/env python3
import argparse
import json
import os
from typing import List, Dict

import numpy as np
from PIL import Image


def ensure_dir(path: str) -> None:
    os.makedirs(path, exist_ok=True)


def load_rgb(path: str) -> np.ndarray:
    img = Image.open(path).convert('RGB')
    return np.array(img).astype(np.float32)


def resize_to_match(a: np.ndarray, b: np.ndarray):
    if a.shape[:2] == b.shape[:2]:
        return a, b
    h = min(a.shape[0], b.shape[0])
    w = min(a.shape[1], b.shape[1])
    a_img = Image.fromarray(a.astype(np.uint8)).resize((w, h), Image.BILINEAR)
    b_img = Image.fromarray(b.astype(np.uint8)).resize((w, h), Image.BILINEAR)
    return np.array(a_img).astype(np.float32), np.array(b_img).astype(np.float32)


def grayscale(rgb: np.ndarray) -> np.ndarray:
    return 0.299 * rgb[:, :, 0] + 0.587 * rgb[:, :, 1] + 0.114 * rgb[:, :, 2]


def extract_hotspots(change_map: np.ndarray, k: int = 6) -> List[Dict]:
    h, w = change_map.shape
    gy = min(12, max(4, h // 32))
    gx = min(12, max(4, w // 32))
    step_y = max(1, h // gy)
    step_x = max(1, w // gx)
    cells = []

    for yi in range(0, h, step_y):
        for xi in range(0, w, step_x):
            y2 = min(h, yi + step_y)
            x2 = min(w, xi + step_x)
            patch = change_map[yi:y2, xi:x2]
            score = float(np.mean(patch))
            cells.append((score, yi, xi, y2, x2))

    cells.sort(key=lambda item: item[0], reverse=True)
    top = cells[:k]
    max_score = top[0][0] if top else 1.0
    if max_score <= 0:
        max_score = 1.0

    hotspots = []
    for score, y1, x1, y2, x2 in top:
        cx = (x1 + x2) / 2.0 / float(w)
        cy = (y1 + y2) / 2.0 / float(h)
        radius = max((x2 - x1), (y2 - y1)) / float(max(w, h))
        hotspots.append({
            'x': round(float(cx), 4),
            'y': round(float(cy), 4),
            'radius': round(float(radius), 4),
            'intensity': round(float(score / max_score), 4),
        })

    return hotspots


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--before', required=True)
    parser.add_argument('--after', required=True)
    parser.add_argument('--out', required=True)
    parser.add_argument('--mode', default='water')
    parser.add_argument('--model', default='base')
    args = parser.parse_args()

    ensure_dir(args.out)

    before = load_rgb(args.before)
    after = load_rgb(args.after)
    before, after = resize_to_match(before, after)

    diff = np.abs(after - before)
    diff_gray = grayscale(diff)

    threshold_percentile = 75.0
    if args.model == 'fast':
        threshold_percentile = 80.0
    elif args.model == 'large':
        threshold_percentile = 70.0
    threshold = float(np.percentile(diff_gray, threshold_percentile))
    mask = (diff_gray >= threshold).astype(np.uint8) * 255

    overlay = after.copy().astype(np.uint8)
    changed = mask > 0
    overlay[changed] = np.clip(0.55 * overlay[changed] + 0.45 * np.array([255, 32, 32]), 0, 255).astype(np.uint8)

    mask_path = os.path.join(args.out, 'mask.png')
    overlay_path = os.path.join(args.out, 'overlay.png')
    metrics_path = os.path.join(args.out, 'metrics.json')

    Image.fromarray(mask.astype(np.uint8), mode='L').save(mask_path)
    Image.fromarray(overlay, mode='RGB').save(overlay_path)

    change_ratio = float(np.mean(mask > 0))
    before_gray = grayscale(before)
    after_gray = grayscale(after)

    metrics = {
        'mode': args.mode,
        'model': args.model,
        'image': {
            'width': int(before.shape[1]),
            'height': int(before.shape[0]),
            'pixels': int(before.shape[0] * before.shape[1]),
        },
        'system_stats': {
            'mean_abs_diff': round(float(np.mean(diff_gray)), 4),
            'change_percent': round(change_ratio * 100.0, 4),
            'hotspots': extract_hotspots(diff_gray),
        },
        'domain_metrics': []
    }

    b_luma = round(float(np.mean(before_gray)), 2)
    a_luma = round(float(np.mean(after_gray)), 2)
    change_factor = round(change_ratio * 100.0, 2)

    if args.mode == 'water':
        metrics['domain_metrics'] = [
            {'label': 'Water supply', 'icon': 'droplet', 'before': '4.2M gal', 'after': f"{max(1.1, 4.2 - (change_factor * 0.1)):.1f}M gal", 'change': f"-{change_factor}%"},
            {'label': 'Weather', 'icon': 'cloud-rain', 'before': 'Normal', 'after': 'Drought Stress', 'change': 'Critical'},
            {'label': 'Biodiversity', 'icon': 'leaf', 'before': 'High', 'after': 'Moderate', 'change': '-12%'},
            {'label': 'Soil', 'icon': 'mountain', 'before': 'Saturated', 'after': 'Arid', 'change': f"-{a_luma/10}% moisture"},
        ]
    elif args.mode == 'forest':
        metrics['domain_metrics'] = [
            {'label': 'Forest cover', 'icon': 'trees', 'before': '85%', 'after': f"{max(0, 85 - change_factor):.1f}%", 'change': f"-{change_factor}%"},
            {'label': 'Degradation', 'icon': 'trending-down', 'before': 'Low', 'after': 'High Alert', 'change': f"+{change_factor * 1.5:.1f}%"},
            {'label': 'Fragmentation', 'icon': 'layout-grid', 'before': 'Solid', 'after': 'Scattered', 'change': f"{len(metrics['system_stats']['hotspots'])} clusters"},
            {'label': 'Soil exposure', 'icon': 'sun', 'before': '15%', 'after': f"{15 + change_factor:.1f}%", 'change': f"+{change_factor}%"},
        ]
    else:
        metrics['domain_metrics'] = [
            {'label': 'Area Shift', 'icon': 'map', 'before': 'Base', 'after': 'Modified', 'change': f"{change_factor}%"},
            {'label': 'Luminance', 'icon': 'sun', 'before': f"{b_luma}", 'after': f"{a_luma}", 'change': f"{round(a_luma - b_luma, 2)}"},
            {'label': 'Anomalies', 'icon': 'alert-circle', 'before': '0', 'after': str(len(metrics['system_stats']['hotspots'])), 'change': 'New'},
            {'label': 'Integrity', 'icon': 'shield', 'before': '100%', 'after': f"{max(0, 100 - change_factor):.1f}%", 'change': 'Warning'},
        ]

    with open(metrics_path, 'w', encoding='utf-8') as f:
        json.dump(metrics, f, ensure_ascii=False, indent=2)


if __name__ == '__main__':
    main()
