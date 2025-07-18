<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help & FAQ - FreelanceHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Help Center</h1>
                <p class="text-xl text-purple-100 mb-8">
                    Find answers to common questions and get the help you need
                </p>
                
                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search for help articles..."
                               class="w-full px-6 py-4 text-gray-900 rounded-lg focus:ring-2 focus:ring-white focus:ring-opacity-50 focus:outline-none text-lg">
                        <button class="absolute right-2 top-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Getting Started</h3>
                    <p class="text-gray-600 mb-4">Learn how to create your account and get started on FreelanceHub</p>
                    <a href="#getting-started" class="text-blue-600 hover:text-blue-800 font-medium">
                        Learn More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <i class="fas fa-credit-card text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Payments</h3>
                    <p class="text-gray-600 mb-4">Understand how payments work and manage your billing</p>
                    <a href="#payments" class="text-green-600 hover:text-green-800 font-medium">
                        Learn More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full mb-4">
                        <i class="fas fa-headset text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Support</h3>
                    <p class="text-gray-600 mb-4">Get help from our support team when you need it</p>
                    <a href="#support" class="text-purple-600 hover:text-purple-800 font-medium">
                        Learn More <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- FAQ Sections -->
        <div class="grid lg:grid-cols-4 gap-8">
            <!-- Categories Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Categories</h3>
                    <nav class="space-y-2">
                        <a href="#getting-started" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-rocket mr-2"></i>Getting Started
                        </a>
                        <a href="#freelancers" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-user-tie mr-2"></i>For Freelancers
                        </a>
                        <a href="#clients" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-briefcase mr-2"></i>For Clients
                        </a>
                        <a href="#payments" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-credit-card mr-2"></i>Payments
                        </a>
                        <a href="#disputes" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-gavel mr-2"></i>Disputes
                        </a>
                        <a href="#security" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>Security
                        </a>
                        <a href="#support" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                            <i class="fas fa-headset mr-2"></i>Support
                        </a>
                    </nav>
                </div>
            </div>

            <!-- FAQ Content -->
            <div class="lg:col-span-3">
                <!-- Getting Started -->
                <section id="getting-started" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Getting Started</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I create an account?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Creating an account is simple! Click the "Sign Up" button in the top right corner, choose whether you're a freelancer or client, fill in your details, and verify your email address. You'll be ready to start using FreelanceHub immediately.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What's the difference between freelancer and client accounts?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Freelancer accounts are for professionals offering services. You can browse and apply for jobs, showcase your portfolio, and receive payments. Client accounts are for businesses or individuals who need work done. You can post jobs, review applications, and hire freelancers.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">Is FreelanceHub free to use?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Creating an account and browsing jobs is free. We charge a small service fee on completed projects to maintain and improve the platform. Freelancers pay a percentage of their earnings, while clients pay processing fees for payments.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- For Freelancers -->
                <section id="freelancers" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">For Freelancers</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I find and apply for jobs?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Browse jobs on your dashboard or use the search function to find specific opportunities. Click on jobs that interest you to view full details, then submit a proposal with your cover letter, portfolio samples, and proposed timeline and budget.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I create a compelling profile?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Include a professional photo, write a clear headline and summary, list your skills and experience, upload portfolio samples, and set competitive rates. Complete profiles get more views and job invitations.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I set my rates?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Research market rates for your skills and experience level. Consider your expertise, project complexity, and client budget. You can set hourly rates or fixed project prices. Start competitive and increase rates as you build reputation and reviews.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- For Clients -->
                <section id="clients" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">For Clients</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I post a job?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Click "Post a Job" from your dashboard. Fill in the job title, description, required skills, budget, and timeline. Be specific about your requirements to attract the right freelancers. Your job will be live once submitted.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I choose the right freelancer?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Review freelancer profiles, portfolios, and ratings. Look for relevant experience and positive client feedback. Consider their proposal quality, communication skills, and proposed timeline. Interview top candidates before making your decision.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What should I include in my job posting?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Include a clear job title, detailed description of work needed, required skills and experience, project timeline, budget range, and any specific requirements. The more detailed your posting, the better quality proposals you'll receive.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Payments -->
                <section id="payments" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Payments</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do payments work?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Clients fund milestone payments into escrow. Freelancers work on deliverables and submit them for approval. Once approved, payments are released to the freelancer's account. This system protects both parties and ensures work is completed before payment.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What payment methods are accepted?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    We accept all major credit cards, PayPal, bank transfers, and digital wallets. Clients can fund their accounts using these methods. Freelancers can withdraw earnings via bank transfer, PayPal, or digital wallets available in their region.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What are the fees?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Freelancers pay a service fee of 5-20% based on their total earnings with each client. Clients pay a 3% processing fee on payments. There are no membership fees or hidden charges. Full fee details are available in your account settings.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How long do withdrawals take?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Withdrawal times vary by method: PayPal (1-3 business days), bank transfer (3-5 business days), and digital wallets (1-2 business days). First-time withdrawals may take longer for verification purposes.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Disputes -->
                <section id="disputes" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Disputes</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I handle a dispute?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    First, try to resolve the issue directly with the other party through our messaging system. If that doesn't work, you can escalate to our dispute resolution team. Provide all relevant documentation and communications to support your case.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What happens during dispute resolution?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Our team reviews all evidence, communications, and project details. Both parties can present their case. We aim to reach a fair resolution within 7-10 business days. Decisions are final and binding, with appropriate actions taken on payments and accounts.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How can I prevent disputes?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Communicate clearly about project requirements, timelines, and expectations. Use our milestone system to break projects into manageable phases. Keep detailed records of all communications and deliverables. Address issues promptly before they escalate.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Security -->
                <section id="security" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Security</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How secure is my data?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    We use industry-standard encryption and security measures to protect your data. All payments are processed through secure, PCI-compliant systems. Your personal information is never shared with third parties without your consent.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I enable two-factor authentication?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Go to your account settings and select "Security." Click "Enable Two-Factor Authentication" and follow the setup instructions. We recommend using an authenticator app for the most secure experience.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What should I do if I suspect fraud?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Report suspicious activity immediately through our security team. Change your password and review your account activity. We have fraud detection systems in place and will investigate any reported incidents promptly.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Support -->
                <section id="support" class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Support</h2>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">How do I contact support?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    You can reach our support team through the "Help" button in your dashboard, email us at support@freelancehub.com, or use the live chat feature. We typically respond within 24 hours during business days.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">What are your support hours?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Our support team is available Monday through Friday, 9 AM to 6 PM EST. Live chat is available during these hours, while email support is monitored 24/7 with responses within 24 hours.
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm" x-data="{ open: false }">
                            <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50 transition-colors">
                                <h3 class="text-lg font-medium text-gray-900">Can I request new features?</h3>
                                <i class="fas fa-chevron-down transform transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="open" x-transition class="px-6 pb-4">
                                <p class="text-gray-700">
                                    Absolutely! We welcome feature requests from our users. You can submit suggestions through our feedback form or contact support directly. We review all requests and prioritize them based on user needs and platform improvements.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- Contact Section -->
    <div class="bg-white border-t">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Need Help? Contact Us</h2>
            <p class="text-lg text-gray-700 mb-4">We're here to help. Get in touch with our team.</p>
            <a href="{{ route('contact') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-envelope mr-2"></i>Contact Us
            </a>
        </div>
    </div>
</x-app-layout>