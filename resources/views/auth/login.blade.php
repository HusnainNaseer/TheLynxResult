<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Lynx School - Result Management System</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <style>
        @import url('https://fonts.cdnfonts.com/css/edwardian-script-itc');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-red: #DC143C;
            --primary-blue: #00b0f0;
            --dark-navy: #1a1a2e;
            --light-gray: #f5f5f5;
            --accent-gold: #FFD700;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            height: 100vh;
        }

        /* Main Container */
        .main-container {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            height: 100vh;
        }

        /* Left Side - Landing Content */
        .landing-side {
            background-image:
                linear-gradient(360deg, rgb(220 20 60 / 52%), #1a1a2ee8),
                url('{{ asset('assets/auth/images/lynx-bg.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Fallback solid background if no image */
        .landing-side.no-image {
            background: linear-gradient(135deg, rgba(220, 20, 60, 0.95), rgba(184, 16, 48, 0.95));
        }

        .landing-side::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
            top: 0;
            right: 0;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 0.3;
            }

            50% {
                opacity: 0.6;
            }
        }

        .landing-content {
            text-align: center;
            z-index: 1;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .landing-content h1 {
            font-family: serif;
            font-size: 2.8rem;
            line-height: 1.3;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
        }

        .landing-content h1::after {
            content: '|';
            animation: blink 0.7s infinite;
            margin-left: 5px;
        }

        @keyframes blink {

            0%,
            50% {
                opacity: 1;
            }

            51%,
            100% {
                opacity: 0;
            }
        }

        .logo-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            border-radius: 50px;
            margin-top: 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        /* Right Side - Login Form - NO ANIMATION */
        .login-side {
            background: #1a1a2e;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.1);
        }

        /* Animation on the form container */
        .login-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
            animation: fadeInScale 0.8s ease-out;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .login-header {
            margin-bottom: 2.5rem;
            text-align: center;
            animation: fadeIn 0.6s ease-out 0.2s backwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            color: var(--dark-navy);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Animate user type selector */
        .user-type-selector {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
            margin-bottom: 2rem;
            animation: fadeIn 0.6s ease-out 0.3s backwards;
        }

        /* Animate form groups */
        .form-group {
            margin-bottom: 1.5rem;
            animation: fadeIn 0.6s ease-out backwards;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.4s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.5s;
        }

        .form-group label {
            display: block;
            color: #999;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(220, 20, 60, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            animation: fadeIn 0.6s ease-out 0.6s backwards;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: #999;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-red);
        }

        .forgot-password {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }

        .forgot-password:hover {
            opacity: 0.8;
        }

        .login-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary-red);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
            animation: fadeIn 0.6s ease-out 0.7s backwards;
        }

        .login-btn:hover {
            background: #b81030;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(220, 20, 60, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #999;
            font-size: 0.85rem;
            animation: fadeIn 0.6s ease-out 0.8s backwards;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e0e0e0;
        }

        .divider span {
            padding: 0 1rem;
        }

        .user-type-btn {
            padding: 0.8rem;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-dark);
        }

        .user-type-btn:hover {
            border-color: var(--primary-red);
        }

        .user-type-btn.active {
            background: var(--primary-red);
            color: white;
            border-color: var(--primary-red);
        }

        .help-text {
            text-align: center;
            margin-top: 2rem;
            color: #666;
            font-size: 0.85rem;
            animation: fadeIn 0.6s ease-out 0.9s backwards;
        }

        .help-text a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .help-text a:hover {
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .main-container {
                grid-template-columns: 1fr;
            }

            .landing-side {
                display: none;
            }

            .login-side {
                box-shadow: none;
            }
        }

        @media (max-width: 768px) {
            .login-side {
                padding: 2rem 1.5rem;
            }

            .login-header h2 {
                font-size: 1.8rem;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
            }
        }

        /* Loading State */
        .login-btn.loading {
            position: relative;
            color: transparent;
        }

        .login-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid white;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .school-name {
            font-family: 'Edwardian Script ITC', cursive !important;
            color: var(--primary-red) !important;
            font-size: 70px !important;
            text-align: center !important;
            margin: 0 !important;
            font-weight: bold !important;
        }

        .logo-img{
            height: 80px;
            position:absolute ;
            top: 20px;
            left: 20px;
        }
    </style>

</head>

<body>
    <div class="main-container">
        <!-- Left Side - Landing Content -->
        <div class="landing-side">
            <img class="logo-img" src="assets/auth/images/lynx logo transparent.png" alt="Lynx logo">

            <div class="landing-content">
                {{-- <h1>Result Management System</h1> --}}
                <h1 id="typingText"></h1>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-side">
            <div class="login-container">
                <div class="login-header">
                    <h2 class="school-name">
                        The Lynx School
                    </h2>

                    {{-- <p>Enter your credentials</p> --}}
                </div>

                <!-- User Type Selection -->
                <div class="user-type-selector">
                    <button type="button" class="user-type-btn active" data-type="Admin">Admin</button>
                    <button type="button" class="user-type-btn" data-type="Teacher">Teacher</button>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <input type="hidden" name="user_type" id="userType" value="Admin">

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required
                            autofocus value="{{ old('email') }}">
                        @error('email')
                            <span
                                style="color: var(--primary-red); font-size: 0.85rem; margin-top: 0.3rem; display: block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password"
                            required>
                        @error('password')
                            <span
                                style="color: var(--primary-red); font-size: 0.85rem; margin-top: 0.3rem; display: block;">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            {{-- <a href="{{ route('password.request') }}" class="forgot-password">Forgot Password?</a> --}}
                        @endif
                    </div>

                    <button type="submit" class="login-btn">Sign In</button>
                </form>

                <div class="divider">
                    <span>Need Help?</span>
                </div>

                <div class="help-text">
                    Having trouble logging in?<br>
                    <a href="mailto:info@thelynxschool.edu.pk">Contact Support</a>
                </div>
            </div>
        </div>
    </div>
    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script>
        const text = "Result Management System";
        const typingText = document.getElementById('typingText');
        let index = 0;

        function type() {
            if (index < text.length) {
                typingText.textContent += text.charAt(index);
                index++;
                setTimeout(type, 100); // Typing speed (100ms per character)
            }
        }

        // Start typing when page loads
        window.addEventListener('load', () => {
            setTimeout(type, 800); // Delay before starting
        });

        // User type selection
        const userTypeBtns = document.querySelectorAll('.user-type-btn');
        const userTypeInput = document.getElementById('userType');

        userTypeBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                userTypeBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                userTypeInput.value = this.getAttribute('data-type');
            });
        });

        // Form submission with loading state
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.querySelector('.login-btn');

        loginForm.addEventListener('submit', function(e) {
            loginBtn.classList.add('loading');
            loginBtn.disabled = true;
        });
    </script>
</body>

</html>
