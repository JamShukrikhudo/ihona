@extends('layouts.app')

@section('content')
    <x-prose-page eyebrow="Legal" title="Privacy Policy"
        {{-- A fixed date, edited when the wording is. now() made the document
             claim to have been revised on whatever day it was read, and on a
             privacy notice the revision date is the load-bearing part. --}}
        updated="15 August 2026">
         
        <p>
            At our real estate platform, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information.
        </p>
         <h2>1. Information We Collect</h2>
        <p>
            We collect personal information that you provide directly to us, such as your name, email address, phone number, and property preferences. We may also collect information about your usage of our platform.
        </p>
         <h2>2. How We Use Your Information</h2>
        <p>
            We use your information to provide and improve our services, communicate with you, and personalize your experience. We may also use your information for marketing purposes, but you can opt out at any time.
        </p>
         <h2>3. Data Protection</h2>
        <p>
            We implement a variety of security measures to maintain the safety of your personal information. However, no method of transmission over the Internet is 100% secure.
        </p>
         <h2>4. Sharing of Information</h2>
        <p>
            We do not sell or rent your personal information to third parties. We may share your information with service providers who assist us in operating our platform or servicing you.
        </p>
         <h2>5. Your Rights</h2>
        <p>
            You have the right to access, correct, or delete your personal information. You may also object to or restrict certain processing of your information.
        </p>
         <h2>6. Cookies</h2>
        <p>
            We use cookies to improve your experience on our platform. You can set your browser to refuse all or some browser cookies, or to alert you when websites set or access cookies.
        </p>
         <h2>7. Changes to This Policy</h2>
        <p>
            We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.
        </p>
         <p class="mt-6">
            If you have any questions about this Privacy Policy, please contact us using the information provided on our Contact page.
        </p>
    </x-prose-page>
@endsection
