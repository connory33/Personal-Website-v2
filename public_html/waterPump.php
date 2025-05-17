<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Water Pump Project - Design and fabrication of a two-cylinder reciprocating pump with scotch yoke mechanism">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | Water Pump Project</title>

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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Water Pump Project</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                Design and fabrication of a high-performance piston pump from raw materials
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Mechanical Engineering</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">CAD Design</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Manufacturing</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Fluid Mechanics</span>
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
                    Our team designed and fabricated a two-cylinder reciprocating pump that exceeded the project requirements, 
                    delivering 5 liters per minute against gravity—five times the required flow rate.
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Project Requirements</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Design and build a pump capable of moving 1 liter/minute against gravity</li>
                        <li>Fabricate all key components from aluminum stock and standard parts</li>
                        <li>Use machine tools (mill and lathe) for component manufacturing</li>
                        <li>Document the complete engineering process from research to testing</li>
                        <li>Optimize for reliability, efficiency, and manufacturability</li>
                    </ul>
                </div>
                
                <p class="mb-4">
                    This project provided a comprehensive engineering experience that spanned research, design, component selection, 
                    fabrication, assembly, and performance testing. Working through the entire product development lifecycle gave us 
                    valuable hands-on experience with both technical and project management skills.
                </p>
            </div>
        </section>
        
        <!-- Design Process Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Design & Engineering Process</h2>
            
            <div class="space-y-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Research & Pump Selection</h3>
                    <p class="mb-4">
                        Our team began by researching various pump technologies to determine the optimal design for our requirements. 
                        We evaluated several pump types including:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <h4 class="font-medium mb-2">Piston Pumps</h4>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Excellent reliability</li>
                                <li>High pressure capability</li>
                                <li>Relatively simple fabrication</li>
                                <li>Good efficiency</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Peristaltic Pumps</h4>
                            <ul class="list-disc pl-5 space-y-1">
                                <li>Gentle fluid handling</li>
                                <li>No fluid contamination</li>
                                <li>Complex roller mechanisms</li>
                                <li>Lower pressure capabilities</li>
                            </ul>
                        </div>
                    </div>
                    <p>
                        After evaluating the trade-offs, we selected a two-cylinder reciprocating piston pump design with a scotch yoke 
                        mechanism. This configuration offered the best combination of reliability, manufacturability, and performance 
                        for our application.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">CAD Design & Optimization</h3>
                    <p class="mb-4">
                        Once we selected the pump type, we developed detailed CAD models to refine the design before fabrication.
                        Key design considerations included:
                    </p>
                    <ul class="space-y-2 list-disc pl-5 mb-4">
                        <li><span class="font-medium">Piston Cylinder Ratio:</span> Optimized for flow rate and pressure requirements</li>
                        <li><span class="font-medium">Scotch Yoke Design:</span> Ensured smooth reciprocating motion with minimal vibration</li>
                        <li><span class="font-medium">Seal Selection:</span> Chose appropriate O-rings for preventing leakage</li>
                        <li><span class="font-medium">Material Selection:</span> Selected aluminum for most components due to machinability and weight</li>
                        <li><span class="font-medium">Assembly Considerations:</span> Designed for ease of manufacturing and maintenance</li>
                    </ul>
                    <p>
                        The CAD modeling phase allowed us to identify potential issues and refine our design before 
                        moving to fabrication, saving time and materials in the manufacturing process.
                    </p>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Fabrication & Assembly</h3>
                    <p class="mb-4">
                        With our design finalized and a complete bill of materials prepared, we proceeded to the fabrication phase:
                    </p>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Machined the aluminum components using a mill and lathe</li>
                        <li>The scotch yoke mechanism required precision turning to achieve the necessary thickness</li>
                        <li>Assembled the pump using standard fasteners, bearings, and O-rings</li>
                        <li>Tested initial operation and made adjustments to ensure smooth movement</li>
                        <li>Finalized the assembly with proper sealing to prevent leaks</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Testing & Results</h3>
                    <p class="mb-4">
                        After assembly, we conducted thorough performance testing to evaluate our pump against the project requirements:
                    </p>
                    <div class="flex items-center justify-center my-6">
                        <div class="bg-blue-50 rounded-lg border border-blue-200 px-6 py-4 w-full max-w-md text-center">
                            <p class="text-lg font-medium text-primary">Required Flow Rate: 1 L/min</p>
                            <div class="h-1 bg-slate-200 my-3 rounded-full overflow-hidden">
                                <div class="bg-primary h-full w-1/5"></div>
                            </div>
                            <p class="text-2xl font-bold text-primary">Achieved: 5 L/min</p>
                            <p class="text-sm text-slate-500 mt-1">500% of required performance</p>
                        </div>
                    </div>
                    <p>
                        Our pump significantly outperformed the project requirements, delivering five times the specified flow rate. 
                        This success validated our design choices and manufacturing precision.
                    </p>
                </div>
            </div>
        </section>
        
        <!-- Project Gallery Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Project Gallery</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/wp_cad.JPG" alt="Water Pump CAD Design">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">CAD Design</h3>
                        <p class="text-slate-600">Initial computer-aided design showing the pump's core components and layout.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/WP1.jpeg" alt="Assembled Water Pump">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Fabricated Pump</h3>
                        <p class="text-slate-600">The completed pump assembly after machining and component integration.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/Assembly_Render2.jpg" alt="Pump Assembly Render">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Assembly Render</h3>
                        <p class="text-slate-600">Detailed 3D rendering showing the complete pump assembly and mechanism.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/Wheel2.jpg" alt="Scotch Yoke Mechanism">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Scotch Yoke Mechanism</h3>
                        <p class="text-slate-600">Close-up of the precision-machined scotch yoke that converts rotational motion to linear motion.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Engineering Reflections Section (Collapsible) -->
        <section class="mb-16">
            <details class="bg-white rounded-lg shadow-sm border border-slate-200">
                <summary class="text-2xl font-bold text-primary p-6 cursor-pointer">
                    Engineering Insights & Reflections
                </summary>
                <div class="p-6 pt-0 border-t border-slate-200">
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-3 text-primary">Technical Challenges</h3>
                        <ul class="space-y-2 list-disc pl-5">
                            <li><span class="font-medium">Scotch Yoke Fabrication:</span> The precision required for the scotch yoke mechanism was the most challenging aspect of fabrication, requiring careful lathe work to achieve the necessary thickness and smooth operation.</li>
                            <li><span class="font-medium">Sealing System:</span> Ensuring proper sealing at the piston-cylinder interface to prevent leakage while maintaining low friction for efficient operation.</li>
                            <li><span class="font-medium">Alignment:</span> Maintaining precise alignment between the dual cylinders and the drive mechanism to ensure smooth operation and prevent binding.</li>
                        </ul>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-xl font-semibold mb-3 text-primary">Engineering Principles Applied</h3>
                        <ul class="space-y-2 list-disc pl-5">
                            <li><span class="font-medium">Fluid Mechanics:</span> Applied principles of fluid flow, pressure, and volume relationships to determine optimal cylinder dimensions.</li>
                            <li><span class="font-medium">Kinematics:</span> Used mechanical motion principles to design the scotch yoke mechanism for converting rotational to linear motion.</li>
                            <li><span class="font-medium">Materials Science:</span> Selected appropriate materials and surface finishes for components based on their mechanical properties and interaction with water.</li>
                            <li><span class="font-medium">Manufacturing Processes:</span> Applied knowledge of machining capabilities and limitations to design parts that could be efficiently fabricated.</li>
                        </ul>
                    </div>
                    
                    <h3 class="text-xl font-semibold mb-3 text-primary">Professional Development</h3>
                    <p class="mb-4">
                        Beyond the technical skills developed, this project provided valuable experience with professional 
                        engineering practices including:
                    </p>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Working effectively in engineering teams with delegated responsibilities</li>
                        <li>Developing formal documentation including bills of materials and design specifications</li>
                        <li>Managing a project through its complete lifecycle from concept to functional product</li>
                        <li>Communicating technical concepts and design decisions across team members</li>
                        <li>Balancing theoretical design with practical manufacturing constraints</li>
                    </ul>
                </div>
            </details>
        </section>
        
        <!-- CTA / Next Project -->
        <section>
            <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-primary mb-4">Interested in more projects?</h2>
                <p class="text-lg mb-6">Check out my other mechanical engineering and design work</p>
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