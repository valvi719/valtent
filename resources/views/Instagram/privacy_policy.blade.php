<!DOCTYPE html>
<html lang="en">
@extends('layouts.app')

@section('title', 'Wallet')

@section('content')
<body>
<div class="parent-container">
    <div class="wallet-container">
        <h2>Privacy Policy</h2>
        <p><strong>Effective Date:</strong> 4-4-2025</p>
        <p>Welcome to <strong>{{ env('APP_NAME') }}</strong>! This Privacy Policy explains how we collect, use, and protect your information when you use our social network platform (the "Service") available at <strong>{{ env('APP_URL') }}</strong>.</p>
        <p>By using our Service, you agree to the collection and use of information in accordance with this policy.</p>

        <h2>1. Information We Collect</h2>
        <ul>
            <li><strong>Personal Information:</strong> Name, email address, profile picture, date of birth, gender, etc. This may include data from social logins (e.g., Facebook, Instagram, Google).</li>
            <li><strong>Content You Share:</strong> Posts, comments, photos, videos, and messages you publish or send through the platform.</li>
            <li><strong>Usage Information:</strong> Pages visited, features used, preferences, and interactions.</li>
            <li><strong>Device and Log Data:</strong> IP address, browser type, device type, OS, and access timestamps.</li>
        </ul>

        <h2>2. How We Use Your Information</h2>
        <ul>
            <li>To provide and improve our services.</li>
            <li>To personalize your experience and content.</li>
            <li>To send notifications, updates, and support messages.</li>
            <li>To moderate content and ensure user safety.</li>
            <li>To analyze performance and enhance the platform.</li>
        </ul>

        <h2>3. Sharing Your Information</h2>
        <p>We <strong>do not sell or rent</strong> your personal data. We may share it:</p>
        <ul>
            <li>With trusted service providers (e.g., hosting, analytics).</li>
            <li>If required by law or to protect legal rights.</li>
            <li>With your permission or direction (e.g., public posts).</li>
        </ul>

        <h2>4. Cookies and Tracking Technologies</h2>
        <p>We use cookies to keep you signed in, understand user behavior, and improve experience. You can manage cookies via your browser settings.</p>

        <h2>5. Your Rights and Choices</h2>
        <p>You have the right to:</p>
        <ul>
            <li>Update or delete your profile information.</li>
            <li>Access your personal data.</li>
            <li>Deactivate or permanently delete your account.</li>
        </ul>
        <p>To exercise your rights, please contact us at <strong>contact.valtent@gmail.com</strong>.</p>
        <h2>6. Data Security</h2>
        <p>We implement industry-standard security measures to protect your data, though no system is 100% secure online.</p>

        <h2>7. Children's Privacy</h2>
        <p>This platform is <strong>not intended for children under 13</strong>. We do not knowingly collect data from children.</p>

        <h2>8. Third-Party Links</h2>
        <p>Our platform may contain links to external websites. We are not responsible for their privacy practices or content.</p>

        <h2>9. Changes to This Policy</h2>
        <p>We may update this Privacy Policy occasionally. We'll notify you about any significant changes through the app or via email.</p>

        <h2>10. Contact Us</h2>
        <p>If you have any questions or concerns about our privacy practices, please contact us:</p>
        <p>
            <strong>{{ env('APP_NAME') }} Team</strong><br>
            Email: <strong>contact.valtent@gmail.com</strong><br>
            Website: <strong>{{ env('APP_URL') }}</strong>
        </p>
        
    </div>
  </div>
</div>
</body>
@endsection
</html>
