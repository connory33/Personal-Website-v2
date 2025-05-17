<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="NHL Line Analysis - Investigating the dependency of NHL teams on their top lines">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | NHL Database</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#1e293b',
                        accent: '#3b82f6'
                    }
                }
            }
        }
    </script>

    <style>
                /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
        .image-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Project header with gradient */
        .project-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            clip-path: polygon(0 0, 100% 0, 100% 85%, 0 100%);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
        </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans">

<!-- Header -->
<?php include 'header.php'; ?>

<!-- Project Header Banner -->
<div class="project-header py-16 md:py-24 mb-8 opacity-90">
    <div class="container mx-auto px-4 md:px-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Project Title</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                Project desc
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Skill/topic 1</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Skill/topic 2</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="container mx-auto px-4 md:px-8 my-8">
    <div class="max-w-4xl mx-auto">
        <!-- Overview Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Project Overview</h2>
            
            <div class="prose prose-slate max-w-none">
                <p class="mb-4 text-lg">
                    Project overview description
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Key Questions/Goals</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Item 1</li>
                        <li>Item 2</li>
                    </ul>
                </div>
                
                <p class="mb-4">
                    Additional info
                </p>
                
            </div>
        </section>
        
        <!-- Visualizations Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Key Findings & Visualizations</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/" alt="NHL Top Line Usage Analysis">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Visual 1</h3>
                        <p class="text-slate-600">Desc 1</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/" alt="NHL Team Performance Correlation">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Visual 2</h3>
                        <p class="text-slate-600">Desc 2</p>
                    </div>
                </div>
            </div>
            
        </section>
        
        <!-- Methodology Section (Collapsible) -->
        <section class="mb-16">
            <details class="bg-white rounded-lg shadow-sm border border-slate-200">
                <summary class="text-2xl font-bold text-primary p-6 cursor-pointer">
                    Methodology & Other Details
                </summary>
                <div class="p-6 pt-0 border-t border-slate-200">
                    <p class="mb-4">
                        High Level info
                    </p>
                    
                    <h3 class="text-xl font-semibold mb-3 mt-6">Data Collection</h3>
                    <ul class="space-y-2 list-disc pl-5 mb-6">
                        <li>Items here</li>
                   
                    </ul>
                    
                    <h3 class="text-xl font-semibold mb-3">Analysis Approach</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Items here</li>
                        
                    </ul>
                </div>
            </details>
        </section>
        
        <!-- Resources & Links -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Project Resources</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="https://github.com/connory33/NHL_shift_analysis" class="group" target="_blank" rel="noopener noreferrer">
                    <div class="flex items-center p-6 bg-white rounded-lg shadow-sm border border-slate-200 transition-all hover:shadow-md">
                        <div class="bg-primary/10 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold group-hover:text-primary transition-colors">GitHub Repository</h3>
                            <p class="text-slate-600">View the complete code and data processing pipeline</p>
                        </div>
                    </div>
                </a>
                
                <a href="resources/NHL_shift_analysis.pdf" class="group" download>
                    <div class="flex items-center p-6 bg-white rounded-lg shadow-sm border border-slate-200 transition-all hover:shadow-md">
                        <div class="bg-primary/10 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold group-hover:text-primary transition-colors">Research Paper</h3>
                            <p class="text-slate-600">Download the full analysis report (PDF)</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- CTA / Next Project -->
        <section>
            <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-primary mb-4">Interested in more projects?</h2>
                <p class="text-lg mb-6">Check out my other engineering, data, and analytics work</p>
                <a href="../index.php" class="inline-block bg-primary hover:bg-primary/90 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    View Portfolio
                </a>
            </div>
        </section>
    </div>
</main>

<!-- Footer -->
<?php include 'footer.php'; ?>

<!-- Optional JavaScript -->
<script>
    // Add scroll animations
    document.addEventListener('DOMContentLoaded', () => {
        // Add fade-in animation to sections when they come into view
        const sections = document.querySelectorAll('section');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        sections.forEach(section => {
            section.style.opacity = "0";
            observer.observe(section);
        });
    });
</script>

</body>
</html>