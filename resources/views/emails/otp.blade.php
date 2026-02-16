<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق - Dabab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
            background-color: #f4f4f4;
            padding: 20px;
            direction: rtl;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        
        .message {
            font-size: 16px;
            color: #666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .otp-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 10px;
            margin: 30px 0;
        }
        
        .otp-code {
            font-size: 48px;
            font-weight: bold;
            color: white;
            letter-spacing: 10px;
            font-family: 'Courier New', monospace;
        }
        
        .otp-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-top: 10px;
        }
        
        .expiry-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
            font-size: 14px;
        }
        
        .warning {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #721c24;
            font-size: 14px;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        
        .footer p {
            margin: 5px 0;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">🚗 Dabab</div>
            <h1>{{ $type === 'login' ? 'رمز تسجيل الدخول' : 'رمز التحقق' }}</h1>
        </div>
        
        <!-- Content -->
        <div class="content">
            <p class="greeting">مرحباً،</p>
            
            <p class="message">
                @if($type === 'login')
                    لقد تلقينا طلباً لتسجيل الدخول إلى حسابك في Dabab.
                    <br>استخدم رمز التحقق التالي لإكمال عملية تسجيل الدخول:
                @else
                    شكراً لاختيارك Dabab!
                    <br>استخدم رمز التحقق التالي لإكمال عملية التسجيل:
                @endif
            </p>
            
            <!-- OTP Box -->
            <div class="otp-box">
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-label">رمز التحقق</div>
            </div>
            
            <!-- Expiry Notice -->
            <div class="expiry-notice">
                ⏰ هذا الرمز صالح لمدة <strong>10 دقائق</strong> فقط
            </div>
            
            <!-- Warning -->
            <div class="warning">
                ⚠️ إذا لم تطلب هذا الرمز، يرجى تجاهل هذه الرسالة. لا تشارك هذا الرمز مع أي شخص.
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <p><strong>Dabab</strong></p>
            <p>نظام خدمات التوصيل والنقل</p>
            <p style="margin-top: 15px; color: #adb5bd; font-size: 12px;">
                هذه رسالة تلقائية، يرجى عدم الرد عليها
            </p>
        </div>
    </div>
</body>
</html>
