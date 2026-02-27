<?php
// MercuryVision Studio - Auth
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - MercuryVision Studio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --bg-primary: #09090b; 
            --bg-secondary: #121214;
            --bg-card: rgba(255, 255, 255, 0.03);
            --bg-card-hover: rgba(255, 255, 255, 0.05);
            
            --gold: #3b82f6;
            --gold-light: #60a5fa;
            --gold-glow: transparent;
            
            --text-primary: #fafafa;
            --text-secondary: #a1a1aa;
            --text-tertiary: #71717a;
            
            --border: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(255, 255, 255, 0.15);
            
            --shadow-sm: 0 4px 12px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.4);
            --shadow-glow: none;

            --danger: #ef4444;
            --success: #22c55e;
            
            --font-family: 'Inter', -apple-system, sans-serif;
            --ease: cubic-bezier(0.16, 1, 0.3, 1);
        }

        [data-theme="light"] {
            --bg-primary: #ffffff;
            --bg-secondary: #f4f4f5;
            --bg-card: #ffffff;
            --bg-card-hover: #fafafa;
            
            --gold: #d97706;
            --gold-light: #f59e0b;
            --gold-glow: rgba(217, 119, 6, 0.15);
            
            --text-primary: #09090b;
            --text-secondary: #52525b;
            --text-tertiary: #a1a1aa;
            
            --border: rgba(0, 0, 0, 0.08);
            --border-hover: rgba(0, 0, 0, 0.15);
            
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: var(--font-family); 
            background: var(--bg-primary); 
            color: var(--text-primary); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            transition: background 0.4s var(--ease), color 0.4s var(--ease);
        }

        .bg-pattern {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -2;
            background-image: radial-gradient(var(--border) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.5;
        }

        .gradient-orb { position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.15; animation: float 25s ease-in-out infinite; z-index: -1; pointer-events: none;}
        .orb-1 { width: 600px; height: 600px; background: radial-gradient(circle, var(--gold), transparent); top: -200px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: radial-gradient(circle, #60a5fa, transparent); bottom: -200px; left: -100px; animation-delay: -10s;}
        
        @keyframes float { 
            0%, 100% { transform: translate(0, 0); } 
            50% { transform: translate(-30px, 30px); } 
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 24px;
            z-index: 10;
        }

        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .auth-header { text-align: center; }
        .logo-icon { 
            width: 48px; height: 48px; 
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%); 
            border-radius: 14px; 
            display: inline-flex; align-items: center; justify-content: center; 
            font-weight: 800; font-size: 18px; color: #fff; 
            box-shadow: var(--shadow-glow); 
            margin-bottom: 20px;
        }
        .auth-title { font-size: 28px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.5px; }
        .auth-subtitle { font-size: 15px; color: var(--text-secondary); }

        .auth-methods { display: flex; flex-direction: column; gap: 12px; }
        .btn-social {
            width: 100%; padding: 12px; background: var(--bg-primary);
            border: 1px solid var(--border); border-radius: 12px;
            color: var(--text-primary); font-size: 14px; font-weight: 600;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            cursor: pointer; transition: all 0.2s var(--ease);
        }
        .btn-social:hover { background: var(--bg-card-hover); border-color: var(--text-tertiary); transform: translateY(-1px); }
        .btn-social img { width: 20px; height: 20px; }

        .divider { display: flex; align-items: center; text-align: center; color: var(--text-tertiary); font-size: 13px; margin: 8px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; border-bottom: 1px solid var(--border); }
        .divider:not(:empty)::before { margin-right: 12px; }
        .divider:not(:empty)::after { margin-left: 12px; }

        .auth-toggle {
            display: flex; background: var(--bg-primary); border: 1px solid var(--border); border-radius: 10px; padding: 4px; margin-bottom: 8px;
        }
        .toggle-btn {
            flex: 1; padding: 8px 0; background: transparent; border: none; border-radius: 8px;
            color: var(--text-secondary); font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
        }
        .toggle-btn.active { background: var(--bg-card-hover); color: var(--text-primary); box-shadow: var(--shadow-sm); border: 1px solid var(--border); }

        .form-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; position: relative; }
        .form-group label { font-size: 13px; font-weight: 500; color: var(--text-secondary); }
        .form-input {
            width: 100%; padding: 12px 16px; background: var(--bg-primary);
            border: 1px solid var(--border); border-radius: 10px;
            color: var(--text-primary); font-size: 15px; font-family: inherit; transition: all 0.2s;
        }
        .form-input:focus { outline: none; border-color: var(--gold); box-shadow: 0 0 0 2px var(--gold-glow); }
        .password-toggle { position: absolute; right: 12px; top: 34px; background: none; border: none; color: var(--text-tertiary); cursor: pointer; padding: 4px; display: flex;}
        .password-toggle:hover { color: var(--text-primary); }

        .auth-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .forgot-link { font-size: 13px; color: var(--gold); text-decoration: none; font-weight: 500; }
        .forgot-link:hover { text-decoration: underline; }

        .terms-checkbox { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 24px; }
        .terms-checkbox input { margin-top: 4px; accent-color: var(--gold); width: 16px; height: 16px; }
        .terms-checkbox label { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
        .terms-checkbox a { color: var(--gold); text-decoration: none; }
        .terms-checkbox a:hover { text-decoration: underline; }

        .btn-primary {
            width: 100%; padding: 14px; background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s; box-shadow: var(--shadow-glow);
        }
        .btn-primary:not(:disabled):hover { transform: translateY(-2px); filter: brightness(1.1); box-shadow: 0 8px 24px var(--gold-glow); }
        .btn-primary:disabled { opacity: 0.7; cursor: not-allowed; transform: none; box-shadow: none; }
        
        [data-theme="light"] .btn-primary { color: #fff; }

        .error-message { color: var(--danger); font-size: 13px; padding: 10px; background: rgba(239, 68, 68, 0.1); border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.2); margin-bottom: 16px; display: none; }
        .success-message { color: var(--success); font-size: 13px; padding: 10px; background: rgba(34, 197, 94, 0.1); border-radius: 8px; border: 1px solid rgba(34, 197, 94, 0.2); margin-bottom: 16px; display: none; }

        .view-section { display: none; }
        .view-section.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: var(--text-secondary); text-decoration: none; margin-bottom: 24px; cursor: pointer; font-weight: 500;}
        .back-link:hover { color: var(--text-primary); }

    </style>
</head>
<body>

    <div class="bg-pattern"></div>
    <div class="gradient-orb orb-1"></div>
    <div class="gradient-orb orb-2"></div>

    <div class="auth-container">
        <div class="auth-card">
            
            <div class="auth-header">
                <div class="logo-icon">MV</div>
                <h1 class="auth-title">Welcome</h1>
                <p class="auth-subtitle">Sign in to your account or create a new one</p>
            </div>

            <!-- Login / Signup Wrapper -->
            <div id="main-view" class="view-section active">
                <div class="auth-methods">
                    <button class="btn-social" id="btn-google">
                        <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                        Continue with Google
                    </button>
                    <button class="btn-social" id="btn-github">
                        <svg viewBox="0 0 24 24" width="20" height="20" xmlns="http://www.w3.org/2000/svg"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12" fill="currentColor"/></svg>
                        Continue with GitHub
                    </button>
                    <div class="divider">or continue with email</div>
                </div>

                <div class="auth-toggle">
                    <button class="toggle-btn active" data-target="form-login">Login</button>
                    <button class="toggle-btn" data-target="form-signup">Sign up</button>
                </div>

                <div id="auth-error" class="error-message"></div>

                <!-- Login Form -->
                <form id="form-login" class="auth-form active">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="login-email" class="form-input" placeholder="name@company.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="login-password" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" tabindex="-1"><i data-lucide="eye" width="18" height="18"></i></button>
                    </div>
                    <div class="auth-actions">
                        <div></div>
                        <a href="#" class="forgot-link" id="btn-show-forgot">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn-primary" id="btn-login">
                        <span>Login</span>
                        <i data-lucide="arrow-right" width="18" height="18"></i>
                    </button>
                </form>

                <!-- Signup Form -->
                <form id="form-signup" class="auth-form" style="display: none;">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="signup-username" class="form-input" placeholder="johndoe" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="signup-email" class="form-input" placeholder="name@company.com" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" id="signup-password" class="form-input" placeholder="••••••••" required minlength="6">
                        <button type="button" class="password-toggle" tabindex="-1"><i data-lucide="eye" width="18" height="18"></i></button>
                    </div>
                    <div class="terms-checkbox">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">I agree to the <a href="/terms">Terms of Service</a> and <a href="/privacy">Privacy Policy</a></label>
                    </div>
                    <button type="submit" class="btn-primary" id="btn-signup">
                        <span>Create account</span>
                        <i data-lucide="user-plus" width="18" height="18"></i>
                    </button>
                </form>
            </div>

            <!-- Forgot Password View -->
            <div id="forgot-view" class="view-section">
                <a class="back-link" id="btn-back-login">
                    <i data-lucide="arrow-left" width="16" height="16"></i> Back to login
                </a>
                
                <div id="forgot-error" class="error-message"></div>
                <div id="forgot-success" class="success-message">Reset link sent! Please check your email.</div>

                <form id="form-forgot">
                    <div class="form-group">
                        <label>Email address</label>
                        <input type="email" id="forgot-email" class="form-input" placeholder="name@company.com" required>
                    </div>
                    <button type="submit" class="btn-primary" id="btn-send-reset">
                        <span>Send Reset Link</span>
                        <i data-lucide="mail" width="18" height="18"></i>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Firebase SDK (v10 modular) -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
        import { 
            getAuth, signInWithEmailAndPassword, createUserWithEmailAndPassword, 
            sendPasswordResetEmail, GoogleAuthProvider, GithubAuthProvider, signInWithPopup, signInWithRedirect,
            updateProfile, onAuthStateChanged
        } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCeMGFoDpdc_FZ7WpVgJVgATHYQSEKSQMs",
            authDomain: "mercuryvision26.firebaseapp.com",
            databaseURL: "https://mercuryvision26-default-rtdb.europe-west1.firebasedatabase.app",
            projectId: "mercuryvision26",
            storageBucket: "mercuryvision26.firebasestorage.app",
            messagingSenderId: "688481376868",
            appId: "1:688481376868:web:d58ac7617adc0e0add5d71",
            measurementId: "G-G82P5WHTGK"
        };

        const app = initializeApp(firebaseConfig);
        const auth = getAuth(app);



        // UI Logic
        lucide.createIcons();

        const toggleBtns = document.querySelectorAll('.toggle-btn');
        const loginForm = document.getElementById('form-login');
        const signupForm = document.getElementById('form-signup');
        const mainView = document.getElementById('main-view');
        const forgotView = document.getElementById('forgot-view');
        const authError = document.getElementById('auth-error');
        const forgotError = document.getElementById('forgot-error');
        const forgotSuccess = document.getElementById('forgot-success');

        toggleBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                toggleBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                if (btn.dataset.target === 'form-login') {
                    loginForm.style.display = 'block';
                    signupForm.style.display = 'none';
                } else {
                    loginForm.style.display = 'none';
                    signupForm.style.display = 'block';
                }
                authError.style.display = 'none';
            });
        });

        document.getElementById('btn-show-forgot').addEventListener('click', (e) => {
            e.preventDefault();
            mainView.classList.remove('active');
            forgotView.classList.add('active');
            authError.style.display = 'none';
            forgotError.style.display = 'none';
            forgotSuccess.style.display = 'none';
        });

        document.getElementById('btn-back-login').addEventListener('click', () => {
            forgotView.classList.remove('active');
            mainView.classList.add('active');
            forgotError.style.display = 'none';
            forgotSuccess.style.display = 'none';
        });

        document.querySelectorAll('.password-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = btn.previousElementSibling;
                const icon = btn.querySelector('i');
                if(input.type === 'password') {
                    input.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    input.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }
                lucide.createIcons();
            });
        });

        function showError(element, message) {
            element.textContent = message;
            element.style.display = 'block';
        }

        const handleAuthSuccess = async (user) => {
            try {
                const idToken = await user.getIdToken(true);
                const res = await fetch('/api/session.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'include',
                    body: JSON.stringify({ action: 'login', idToken })
                });
                
                if (!res.ok) throw new Error('Failed to create server session.');
                window.location.href = '/dashboard';
            } catch (err) {
                showError(authError, err.message);
                await auth.signOut();
            }
        };

        const getFriendlyErrorMessage = (error) => {
            switch(error.code) {
                case 'auth/invalid-credential': return 'Invalid email or password.';
                case 'auth/email-already-in-use': return 'An account with this email already exists.';
                case 'auth/weak-password': return 'Password should be at least 6 characters.';
                case 'auth/user-not-found': return 'No user found with this email.';
                case 'auth/popup-blocked': return 'Popup was blocked by your browser. Please allow popups and try again.';
                case 'auth/popup-closed-by-user': return 'Popup was closed before completing sign in.';
                default: return error.message;
            }
        };

        const handleSocialLogin = async (provider) => {
            try {
                const result = await signInWithPopup(auth, provider);
                if (result?.user && !isRedirecting) {
                    isRedirecting = true;
                    await handleAuthSuccess(result.user);
                }
            } catch (error) {
                if (error?.code === 'auth/popup-blocked') {
                    const useRedirect = window.confirm('Popup is blocked. Continue using same-tab redirect sign in?');
                    if (useRedirect) {
                        await signInWithRedirect(auth, provider);
                        return;
                    }
                }
                showError(authError, getFriendlyErrorMessage(error));
            }
        };

        // Global auth state listener to automatically handle existing sessions and redirects.
        let isRedirecting = false;
        onAuthStateChanged(auth, async (user) => {
            if (user && !isRedirecting) {
                isRedirecting = true;
                await handleAuthSuccess(user);
            }
        });

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-login');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Authenticating...';
            lucide.createIcons();
            
            try {
                const email = document.getElementById('login-email').value;
                const password = document.getElementById('login-password').value;
                await signInWithEmailAndPassword(auth, email, password);
                // Redirect will be handled by onAuthStateChanged
            } catch (error) {
                showError(authError, getFriendlyErrorMessage(error));
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Login</span><i data-lucide="arrow-right" width="18" height="18"></i>';
                lucide.createIcons();
            }
        });

        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-signup');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Creating account...';
            lucide.createIcons();
            
            try {
                const username = document.getElementById('signup-username').value;
                const email = document.getElementById('signup-email').value;
                const password = document.getElementById('signup-password').value;
                
                const userCredential = await createUserWithEmailAndPassword(auth, email, password);
                await updateProfile(userCredential.user, { displayName: username });
                // Redirect will be handled by onAuthStateChanged
            } catch (error) {
                showError(authError, getFriendlyErrorMessage(error));
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Create account</span><i data-lucide="user-plus" width="18" height="18"></i>';
                lucide.createIcons();
            }
        });

        document.getElementById('form-forgot').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btn-send-reset');
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="animate-spin"></i> Sending...';
            lucide.createIcons();
            
            try {
                const email = document.getElementById('forgot-email').value;
                await sendPasswordResetEmail(auth, email);
                forgotError.style.display = 'none';
                forgotSuccess.style.display = 'block';
                setTimeout(() => {
                    document.getElementById('btn-back-login').click();
                }, 3000);
            } catch (error) {
                forgotSuccess.style.display = 'none';
                showError(forgotError, getFriendlyErrorMessage(error));
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Send Reset Link</span><i data-lucide="mail" width="18" height="18"></i>';
                lucide.createIcons();
            }
        });

        // Social providers via popup (stays on auth page and opens provider window)
        document.getElementById('btn-google').addEventListener('click', async () => {
            const provider = new GoogleAuthProvider();
            await handleSocialLogin(provider);
        });

        document.getElementById('btn-github').addEventListener('click', async () => {
            const provider = new GithubAuthProvider();
            await handleSocialLogin(provider);
        });
        
    </script>
    <style>
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</body>
</html>
