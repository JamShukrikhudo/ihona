@extends('layouts.app')

@section('content')
    <x-prose-page eyebrow="Legal" title="Terms and Conditions"
        :updated="now()->format('j F Y')">
         
        <p>
            Welcome to our real estate platform. By using our services, you agree to comply with and be bound by the following terms and conditions:
        </p>
         <h2>1. Acceptance of Terms</h2>
        <p>
            By accessing or using our platform, you agree to be bound by these Terms and Conditions and all applicable laws and regulations. If you do not agree with any part of these terms, you may not use our services.
        </p>
         <h2>2. Use of Services</h2>
        <p>
            Our platform provides real estate listings and related services. You agree to use these services only for lawful purposes and in accordance with these Terms and Conditions.
        </p>
         <h2>3. User Accounts</h2>
        <p>
            To access certain features of our platform, you may be required to create an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.
        </p>
         <h2>4. Property Listings</h2>
        <p>
            We strive to provide accurate and up-to-date property listings. However, we do not guarantee the accuracy, completeness, or availability of any listing. Users are advised to verify all information independently.
        </p>
         <h2>5. Intellectual Property</h2>
        <p>
            All content on this platform, including text, graphics, logos, and software, is the property of our company or our content suppliers and is protected by copyright laws.
        </p>
         <h2>6. Limitation of Liability</h2>
        <p>
            Our company shall not be liable for any direct, indirect, incidental, consequential, or punitive damages arising out of your access to, or use of, the platform.
        </p>
         <h2>7. Governing Law</h2>
        <p>
            These Terms and Conditions shall be governed by and construed in accordance with the laws of the jurisdiction where our company is registered, without regard to its conflict of law provisions.
        </p>
         <h2>8. Changes to Terms</h2>
        <p>
            We reserve the right to modify these Terms and Conditions at any time. Your continued use of the platform after any such changes constitutes your acceptance of the new Terms and Conditions.
        </p>
         <p class="mt-6">
            If you have any questions about these Terms and Conditions, please contact us using the information provided on our Contact page.
        </p>
    </x-prose-page>
@endsection
