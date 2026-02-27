from fastapi import FastAPI, UploadFile, File
from PIL import Image
import torch
import torchvision.transforms as T
import numpy as np
import io, base64

from models.ChangeFormer import ChangeFormerV6  # подправь под свою версию

app = FastAPI()

model = ChangeFormerV6()
model.load_state_dict(torch.load("pretrained/ChangeFormer.pt", map_location="cpu"))
model.eval()

transform = T.Compose([T.Resize((256, 256)), T.ToTensor()])

@app.post("/predict")
async def predict(img1: UploadFile = File(...), img2: UploadFile = File(...)):
    img1 = Image.open(io.BytesIO(await img1.read())).convert("RGB")
    img2 = Image.open(io.BytesIO(await img2.read())).convert("RGB")

    t1 = transform(img1).unsqueeze(0)
    t2 = transform(img2).unsqueeze(0)

    with torch.no_grad():
        output = model(t1, t2)
        mask = torch.argmax(output, dim=1).squeeze().numpy()

    # возвращаем маску как base64 PNG
    result_img = Image.fromarray((mask * 255).astype(np.uint8))
    buf = io.BytesIO()
    result_img.save(buf, format="PNG")
    encoded = base64.b64encode(buf.getvalue()).decode()

    return {"mask_base64": encoded}