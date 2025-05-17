<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Thermistor Cleaner - An automated device for cleaning food thermometer probes in dining halls">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | Thermistor Cleaner</title>

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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Thermistor Cleaner</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                An automated solution for food safety and cross-contamination prevention
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Product Design</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Food Safety</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Mechanical Engineering</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Human-Centered Design</span>
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
                    The Thermistor Cleaner is a user-friendly device that automates the cleaning of food thermometer probes in dining halls,
                    reducing cross-contamination risk and ensuring food safety compliance with the simple push of a button.
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Problem Statement</h3>
                    <p class="mb-4">
                        In campus dining halls, the same thermometer probe is frequently used to check the temperature of multiple food trays.
                        Without proper cleaning between measurements, this creates a significant cross-contamination risk. Staff often neglect 
                        thorough cleaning due to time constraints or inattention, compromising food safety standards.
                    </p>
                    <p>
                        Our challenge was to design an automated solution that would ensure consistent, thorough cleaning of the thermistor 
                        probe between each use, regardless of staff attentiveness or time pressure.
                    </p>
                </div>
                
                <p class="mb-4">
                    After conducting interviews with dining hall staff to understand their workflow and pain points, we developed a device 
                    that attaches directly to standard food thermometers. Our solution features a mechanical scraping mechanism combined 
                    with an internal brush system that activates with a single button press, effectively removing all food residue from 
                    the probe in seconds.
                </p>
            </div>
        </section>
        
        <!-- Design Process Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Design Process</h2>
            
            <div class="space-y-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Research & Discovery</h3>
                    <p class="mb-4">
                        We began with user research, interviewing dining hall workers to identify key pain points in their food 
                        temperature measurement workflow. This revealed the critical issue of thermometer probe cross-contamination 
                        caused by inconsistent cleaning practices between readings.
                    </p>
                    <p>
                        Our primary insights:
                    </p>
                    <ul class="space-y-2 list-disc pl-5 mt-3">
                        <li>Staff needed to regularly measure multiple food trays in succession</li>
                        <li>The same thermometer probe was used across different food items</li>
                        <li>Thorough cleaning between measurements was often overlooked or rushed</li>
                        <li>Any solution needed to be quick, simple, and require minimal effort</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Conceptual Development</h3>
                    <p class="mb-4">
                        After evaluating several potential approaches, we determined that a mechanical scraping mechanism 
                        combined with a brushing system would provide the most thorough cleaning. This dual-action approach 
                        would remove both surface residue and more stubborn food particles.
                    </p>
                    <p>
                        Initial design considerations included:
                    </p>
                    <ul class="space-y-2 list-disc pl-5 mt-3">
                        <li>How to create linear motion to travel along the probe length</li>
                        <li>Ensuring the cleaning mechanism completely surrounds the probe</li>
                        <li>Power requirements and activation method</li>
                        <li>Attachment mechanism to standard thermometers</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Engineering Solutions</h3>
                    <p class="mb-3">
                        Our initial investigation into linear actuators revealed they were beyond our project budget. 
                        We pivoted to using solenoids, which offered a more cost-effective solution but presented a significant 
                        challenge: their limited range of motion (approximately 2mm).
                    </p>
                    <p>
                        To overcome this limitation, we designed an innovative lever system that amplified the solenoid's 
                        movement, extending the effective range from 2mm to approximately 2 inches—sufficient to clean the 
                        entire probe length.
                    </p>
                    <p class="mt-3">
                        Key components of our final design:
                    </p>
                    <ul class="space-y-2 list-disc pl-5 mt-3">
                        <li>Laser-cut acrylic housing for durability and aesthetic appeal</li>
                        <li>3D-printed lightweight lever arms to minimize inertia</li>
                        <li>Solenoid-activated mechanical system</li>
                        <li>Integrated brush and scraper for thorough cleaning</li>
                        <li>Single-button activation for ease of use</li>
                    </ul>
                </div>
            </div>
        </section>
        
        <!-- Product Gallery Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Product Gallery</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/therm_parts.JPG" alt="Thermistor Cleaner Components">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Component Assembly</h3>
                        <p class="text-slate-600">Exploded view showing the key mechanical components including the solenoid, lever system, and cleaning mechanism.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/therm_top_inside.JPG" alt="Internal Mechanism">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Internal Mechanism</h3>
                        <p class="text-slate-600">Overhead view of the internal lever system that amplifies the solenoid's motion for full-length probe cleaning.</p>
                    </div>
                </div>
            </div>
            
            <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200 mb-8">
                <img class="w-full h-64 object-cover object-center" src="../resources/images/therm_side.JPG" alt="Side Profile View">
                <div class="p-4">
                    <h3 class="font-semibold text-lg mb-2">Side Profile</h3>
                    <p class="text-slate-600">Side view showing the streamlined design and the relationship between the cleaning mechanism and thermometer probe.</p>
                </div>
            </div>
            
            <p class="text-center text-slate-600 italic">
                The compact design attaches directly to standard food thermometers without impeding normal operation.
            </p>
        </section>
        
        <!-- Technical Specifications Section (Collapsible) -->
        <section class="mb-16">
            <details class="bg-white rounded-lg shadow-sm border border-slate-200">
                <summary class="text-2xl font-bold text-primary p-6 cursor-pointer">
                    Technical Specifications & Features
                </summary>
                <div class="p-6 pt-0 border-t border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Mechanical Specifications</h3>
                            <ul class="space-y-2 list-disc pl-5 mb-6">
                                <li><span class="font-medium">Dimensions:</span> 4" × 2.5" × 1.5" (approx.)</li>
                                <li><span class="font-medium">Materials:</span> Laser-cut acrylic housing, 3D printed PLA lever arms</li>
                                <li><span class="font-medium">Actuation:</span> 5V solenoid with lever amplification system</li>
                                <li><span class="font-medium">Cleaning Range:</span> 2" vertical travel</li>
                                <li><span class="font-medium">Weight:</span> Approximately 150g</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">User Features</h3>
                            <ul class="space-y-2 list-disc pl-5">
                                <li><span class="font-medium">One-Touch Operation:</span> Single button activation for complete cleaning cycle</li>
                                <li><span class="font-medium">Universal Attachment:</span> Compatible with standard food thermometers</li>
                                <li><span class="font-medium">Quick Cleaning:</span> Complete probe cleaning in under 3 seconds</li>
                                <li><span class="font-medium">Dual-Action:</span> Combined scraping and brushing for thorough cleaning</li>
                                <li><span class="font-medium">Replaceable Parts:</span> Easily serviced cleaning components</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-xl font-semibold mb-3 text-primary">Design Considerations</h3>
                        <p class="mb-4">
                            The final design reflects careful attention to both practical functionality and user experience:
                        </p>
                        <ul class="space-y-2 list-disc pl-5">
                            <li>The transparent housing allows for visual confirmation of the cleaning process</li>
                            <li>The lever system was carefully balanced to maximize range while maintaining cleaning pressure</li>
                            <li>Button placement was optimized for thumb operation while holding the thermometer</li>
                            <li>Mechanical components were designed for durability in a food service environment</li>
                        </ul>
                    </div>
                </div>
            </details>
        </section>
        
        <!-- Impact & Outcomes Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Impact & Learning Outcomes</h2>
            
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary">Project Impact</h3>
                <p class="mb-4">
                    The Thermistor Cleaner demonstrates how thoughtful engineering design can address everyday workplace challenges:
                </p>
                <ul class="space-y-2 list-disc pl-5">
                    <li>Improves food safety by ensuring consistent probe cleaning between measurements</li>
                    <li>Reduces the risk of cross-contamination in institutional food service settings</li>
                    <li>Simplifies compliance with food safety protocols for staff members</li>
                    <li>Shows how mechanical amplification can overcome actuator limitations in compact devices</li>
                </ul>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">Key Learnings</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium mb-2">Technical Skills</h4>
                        <ul class="space-y-1 list-disc pl-5">
                            <li>Solenoid actuation and power requirements</li>
                            <li>Mechanical advantage systems design</li>
                            <li>CAD modeling for laser cutting and 3D printing</li>
                            <li>Rapid prototyping and iterative design</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-medium mb-2">Design Process</h4>
                        <ul class="space-y-1 list-disc pl-5">
                            <li>User-centered design research methods</li>
                            <li>Budget-constrained engineering solutions</li>
                            <li>Effective stakeholder interviewing</li>
                            <li>Constraint-driven innovation</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- CTA / Next Project -->
        <section>
            <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-primary mb-4">Interested in more projects?</h2>
                <p class="text-lg mb-6">Check out my other product design and engineering work</p>
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