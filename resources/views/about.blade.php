@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-blue-600 to-purple-700 text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold mb-6">About Our Platform</h1>
        <p class="text-xl md:text-2xl max-w-3xl mx-auto">
            Connecting talented freelancers with amazing clients since 2020
        </p>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Our Story</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Founded in 2020, our platform was born from a simple idea: to create a space where talented freelancers 
                    and innovative clients can connect, collaborate, and create amazing things together.
                </p>
                <p class="text-lg text-gray-600 mb-6">
                    We believe that the future of work is flexible, global, and driven by talent rather than location. 
                    Our mission is to break down barriers and make it easier for skilled professionals to find meaningful 
                    work while helping businesses access the best talent from around the world.
                </p>
                <p class="text-lg text-gray-600">
                    Today, we're proud to be home to thousands of freelancers and clients who trust us to facilitate 
                    their professional relationships and help them achieve their goals.
                </p>
            </div>
            <div class="lg:order-first">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg p-8 text-white">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">5000+</div>
                            <div class="text-sm">Active Freelancers</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">2000+</div>
                            <div class="text-sm">Happy Clients</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">10,000+</div>
                            <div class="text-sm">Projects Completed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold mb-2">50+</div>
                            <div class="text-sm">Countries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Our Values</h2>
            <p class="text-lg text-gray-600">The principles that guide everything we do</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Trust & Transparency</h3>
                <p class="text-gray-600">We believe in building relationships based on trust, with transparent processes and fair dealings for all.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Quality First</h3>
                <p class="text-gray-600">We're committed to maintaining the highest standards of quality in every project and interaction.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Community Focus</h3>
                <p class="text-gray-600">We're building a supportive community where everyone can thrive and grow together.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Innovation</h3>
                <p class="text-gray-600">We continuously innovate to provide better tools and experiences for our users.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Passion</h3>
                <p class="text-gray-600">We're passionate about what we do and committed to helping others achieve their dreams.</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Global Reach</h3>
                <p class="text-gray-600">We embrace diversity and believe in connecting talent from all corners of the world.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p class="text-lg text-gray-600">Simple steps to get started</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-blue-600">1</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Sign Up</h3>
                <p class="text-gray-600">Create your free account as a freelancer or client in just a few minutes.</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-green-600">2</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Connect</h3>
                <p class="text-gray-600">Browse projects or post your services. Find the perfect match for your skills.</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-2xl font-bold text-purple-600">3</span>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Collaborate</h3>
                <p class="text-gray-600">Work together seamlessly with our built-in tools and secure payment system.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Meet Our Team</h2>
            <p class="text-lg text-gray-600">The people behind the platform</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">John Doe</h3>
                <p class="text-gray-600 text-sm mb-2">CEO & Founder</p>
                <p class="text-gray-500 text-sm">Leading the vision and strategy</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Jane Smith</h3>
                <p class="text-gray-600 text-sm mb-2">CTO</p>
                <p class="text-gray-500 text-sm">Building amazing technology</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Mike Johnson</h3>
                <p class="text-gray-600 text-sm mb-2">Head of Design</p>
                <p class="text-gray-500 text-sm">Crafting beautiful experiences</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="w-24 h-24 bg-gray-300 rounded-full mx-auto mb-4"></div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Sarah Wilson</h3>
                <p class="text-gray-600 text-sm mb-2">Head of Marketing</p>
                <p class="text-gray-500 text-sm">Connecting with our community</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-purple-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">Ready to Join Our Community?</h2>
        <p class="text-xl mb-8 max-w-2xl mx-auto">
            Whether you're a freelancer or looking to hire, we're here to help you succeed.
        </p>
        <div class="flex flex-col sm