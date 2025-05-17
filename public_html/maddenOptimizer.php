<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Madden Roster Optimizer - Optimizing NFL team rosters with evolutionary algorithms and salary cap constraints">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | Madden Roster Optimizer</title>

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

    <!-- Custom styles -->
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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Madden Roster Optimizer</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                Building the optimal NFL team roster under salary cap constraints
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Optimization</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Excel Evolutionary Solver</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Sports Analytics</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">NFL</span>
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
                    This project explores the challenge of optimizing NFL team rosters using Madden ratings and salaries, revealing 
                    the complex trade-offs between player quality, scheme fit, and salary cap constraints.
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Key Insights</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>There's no single definition of "best" roster - optimization requires balancing competing factors</li>
                        <li>Scheme fit dramatically influences optimal player selections and team composition</li>
                        <li>Evolutionary algorithms provide flexible solutions to multi-constraint optimization problems</li>
                        <li>Qualitative roster preferences can be mathematically encoded through penalty and bonus weights</li>
                    </ul>
                </div>
                
                <p class="mb-4">
                    I set out to determine the "best" possible NFL roster under salary cap constraints using Madden player ratings. 
                    I quickly discovered that there's no mathematically optimal solution when accounting for position-specific needs
                    and scheme considerations.
                </p>
                
                <p class="mb-4">
                    To address this complexity, I leveraged Excel's evolutionary solver, which allows for weighted optimization with 
                    customizable bonuses and penalties. This approach enabled me to explore questions like whether to prioritize a 
                    few elite players or build deeper roster depth, and how to balance competing needs across different positions.
                </p>
                
                <p class="mb-4">
                    The incorporation of Madden's "scheme" definitions added another dimension to the optimization. By utilizing 
                    player ratings in individual skill categories and archetypes, I could create specialized lineups tailored to 
                    different offensive and defensive philosophies.
                </p>
            </div>
        </section>
        
        <!-- Optimization Approach Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Optimization Approach</h2>
            
            <p class="mb-6">
                After organizing the player data and setting up scheme-specific worksheets, I implemented a comprehensive 
                system of penalties and bonuses to guide the evolutionary solver:
            </p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Penalties</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li><span class="font-medium">Hard Constraints:</span> Severe penalties for exceeding salary cap or position limits</li>
                        <li><span class="font-medium">Age Balance:</span> Penalties for excessively high roster average age</li>
                        <li><span class="font-medium">Low Ratings:</span> Discouragement for selecting unrealistically poor players</li>
                        <li><span class="font-medium">Scheme Mismatch:</span> Penalties for players poorly suited to the chosen scheme</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Bonuses</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li><span class="font-medium">Elite Talent:</span> Bonuses for exceptionally high-rated players</li>
                        <li><span class="font-medium">Chemistry:</span> Rewards for college teammate pairings</li>
                        <li><span class="font-medium">Special Attributes:</span> Preferences for players with Superstar or Hidden designations</li>
                        <li><span class="font-medium">Scheme Fit:</span> Bonuses for players who excel in the selected scheme's required skills</li>
                    </ul>
                </div>
            </div>
            
            <p class="mb-4">
                This approach allowed me to mathematically encode qualitative roster-building preferences while maintaining the 
                practical constraints of NFL roster construction. The results provided fascinating insights into how different 
                weighting schemes lead to drastically different optimal rosters.
            </p>
        </section>
        
        <!-- Visualizations Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Results & Visualizations</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/madden1.PNG" alt="Madden Roster Optimization Dashboard">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Optimization Dashboard</h3>
                        <p class="text-slate-600">Primary interface showing roster composition and optimization parameters.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/madden2.PNG" alt="Scheme-Based Optimization Results">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Scheme Optimization</h3>
                        <p class="text-slate-600">Comparison of roster optimization results across different offensive schemes.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/madden3.PNG" alt="Madden Scheme Definitions">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Scheme Definitions</h3>
                        <p class="text-slate-600">Madden's position-specific scheme requirements and skill priorities.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/madden4.PNG" alt="Roster Analysis Output">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Roster Analysis</h3>
                        <p class="text-slate-600">Detailed breakdown of optimized roster performance metrics and salary allocation.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Future Work Section (Collapsible) -->
        <section class="mb-16">
            <details class="bg-white rounded-lg shadow-sm border border-slate-200">
                <summary class="text-2xl font-bold text-primary p-6 cursor-pointer">
                    Future Improvements & Extensions
                </summary>
                <div class="p-6 pt-0 border-t border-slate-200">
                    <p class="mb-4">
                        While this project provides valuable insights into NFL roster optimization, several opportunities for enhancement remain:
                    </p>
                    
                    <ul class="space-y-2 list-disc pl-5 mb-6">
                        <li><span class="font-medium">Dynamic Weight Calibration:</span> Refining the penalty and bonus weights based on real-world roster construction outcomes</li>
                        <li><span class="font-medium">Multi-Year Optimization:</span> Extending the model to account for contract structures, rookie development, and future salary cap implications</li>
                        <li><span class="font-medium">Advanced Metrics Integration:</span> Incorporating additional performance metrics beyond Madden ratings, such as advanced analytics from PFF or Next Gen Stats</li>
                        <li><span class="font-medium">Draft Strategy Module:</span> Creating a complementary tool to optimize draft selection strategies based on roster needs and value projections</li>
                        <li><span class="font-medium">Player Development Modeling:</span> Adding projected player development curves to optimize for both current performance and future potential</li>
                    </ul>
                    
                    <p>
                        These enhancements would bring the optimization model closer to the nuanced reality of NFL front office decision-making.
                    </p>
                </div>
            </details>
        </section>
        
        <!-- Resources & Links -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Project Resources</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="resources/6760_NFL_roster_optimization.xlsx" class="group" download>
                    <div class="flex items-center p-6 bg-white rounded-lg shadow-sm border border-slate-200 transition-all hover:shadow-md">
                        <div class="bg-primary/10 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold group-hover:text-primary transition-colors">Excel Workbook</h3>
                            <p class="text-slate-600">Download the complete roster optimization model</p>
                        </div>
                    </div>
                </a>
                
                <a href="resources/6760_NFL_roster_optimization.pptx" class="group" download>
                    <div class="flex items-center p-6 bg-white rounded-lg shadow-sm border border-slate-200 transition-all hover:shadow-md">
                        <div class="bg-primary/10 rounded-full p-3 mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold group-hover:text-primary transition-colors">Presentation</h3>
                            <p class="text-slate-600">View the project methodology and key findings</p>
                        </div>
                    </div>
                </a>
            </div>
        </section>
        
        <!-- CTA / Next Project -->
        <section>
            <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-primary mb-4">Interested in more projects?</h2>
                <p class="text-lg mb-6">Check out my other optimization and sports analytics work</p>
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