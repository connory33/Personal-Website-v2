<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Autonomous Robot - A competitive block-pushing robot built with Arduino, sensors, and custom electronics">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | Autonomous Robot</title>

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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Autonomous Robot</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                Design and implementation of a competitive block-pushing robot
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Robotics</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Arduino</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Electronics</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">C Programming</span>
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
                    This project involved designing, building, and programming an autonomous robot for a competitive 
                    block-pushing challenge, requiring integration of mechanical design, sensors, and strategic programming.
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Competition Rules</h3>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Each robot competes in a 1v1 match on a rectangular surface divided into blue and yellow halves</li>
                        <li>Plastic cubes are distributed across the board at the start of each match</li>
                        <li>Robots start at the back edge of their respective halves</li>
                        <li>The goal is to push blocks to the opponent's side of the board</li>
                        <li>The robot with fewer blocks on its side after 30 seconds wins</li>
                    </ul>
                </div>
                
                <p class="mb-4">
                    To succeed in this competition, we needed to develop an optimal battle strategy and then build a robot
                    capable of executing it reliably. Our approach integrated sensors for environmental awareness, custom
                    electronics for motor control, and strategic programming to optimize the robot's behavior.
                </p>
            </div>
        </section>
        
        <!-- Technical Implementation Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Technical Implementation</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-4 text-primary">Electronics & Sensors</h3>
                    <ul class="space-y-3 list-disc pl-5">
                        <li><span class="font-medium">Color Sensor:</span> Integrated to detect the robot's position and identify region boundaries</li>
                        <li><span class="font-medium">H-bridge Circuits:</span> Constructed dual H-bridges for independent wheel control with PWM capability</li>
                        <li><span class="font-medium">Arduino:</span> Used as the main microcontroller for processing sensor inputs and controlling motors</li>
                        <li><span class="font-medium">Power Management:</span> Designed efficient power distribution to maximize runtime during competitions</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-4 text-primary">Mechanical Design</h3>
                    <ul class="space-y-3 list-disc pl-5">
                        <li><span class="font-medium">Snowplow Attachment:</span> Custom-fabricated from scrap plastic to increase block-clearing efficiency</li>
                        <li><span class="font-medium">Protective Housing:</span> Laser-cut acrylic enclosure to shield electronic components</li>
                        <li><span class="font-medium">Strategic Mass Distribution:</span> Added weight to improve stability and pushing power</li>
                        <li><span class="font-medium">Wheel Configuration:</span> Optimized for maneuverability and reliable navigation</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 mb-8">
                <h3 class="text-xl font-semibold mb-4 text-primary">Software Strategy</h3>
                <p class="mb-4">
                    The robot was programmed using Arduino and C, with careful attention to low-level optimization
                    through bit manipulation and direct register access. Our algorithmic approach included:
                </p>
                <ul class="space-y-3 list-disc pl-5">
                    <li><span class="font-medium">Boundary Detection:</span> Using the color sensor to identify when the robot crossed between regions</li>
                    <li><span class="font-medium">Movement Patterns:</span> Implementing strategic sweeping motions to efficiently clear blocks</li>
                    <li><span class="font-medium">Time Management:</span> Programming staged behaviors to maximize effectiveness during the 30-second match</li>
                    <li><span class="font-medium">Edge Recovery:</span> Developing reliable routines to recover when approaching arena boundaries</li>
                </ul>
            </div>
        </section>
        
        <!-- Robot Gallery Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Robot Gallery</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/robot.jpg" alt="Autonomous Robot Front View">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Front View</h3>
                        <p class="text-slate-600">The robot with its custom snowplow attachment designed for block pushing.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/robot1.jpg" alt="Robot Top View with Electronics">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Top View</h3>
                        <p class="text-slate-600">Overhead view showing the Arduino, sensor integration, and main circuit board.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/robot3.jpg" alt="Robot Circuit Detail">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Circuit Detail</h3>
                        <p class="text-slate-600">Close-up of the H-bridge circuits used for motor control and PWM implementation.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/robot4.jpg" alt="Robot Competition Setup">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Testing Configuration</h3>
                        <p class="text-slate-600">The robot during testing phases, demonstrating its mobility and block-pushing capability.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Technical Challenges Section (Collapsible) -->
        <section class="mb-16">
            <details class="bg-white rounded-lg shadow-sm border border-slate-200">
                <summary class="text-2xl font-bold text-primary p-6 cursor-pointer">
                    Technical Challenges & Solutions
                </summary>
                <div class="p-6 pt-0 border-t border-slate-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Challenges</h3>
                            <ul class="space-y-2 list-disc pl-5 mb-6">
                                <li><span class="font-medium">Color Sensor Reliability:</span> Initial issues with inconsistent surface readings</li>
                                <li><span class="font-medium">Motor Response Time:</span> Delays between sensor input and movement adjustments</li>
                                <li><span class="font-medium">Edge Detection:</span> Difficulty in accurately identifying arena boundaries</li>
                                <li><span class="font-medium">Pushing Force:</span> Balancing weight distribution for optimal block movement</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-primary">Solutions</h3>
                            <ul class="space-y-2 list-disc pl-5">
                                <li><span class="font-medium">Sensor Calibration:</span> Implemented dynamic calibration routines at startup</li>
                                <li><span class="font-medium">Optimized Code:</span> Used bit manipulation for faster processing</li>
                                <li><span class="font-medium">Algorithm Refinement:</span> Developed more sophisticated boundary detection logic</li>
                                <li><span class="font-medium">Mechanical Redesign:</span> Modified the snowplow shape and angle for better block control</li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h3 class="text-xl font-semibold mb-3 text-primary">Learning Outcomes</h3>
                        <p class="mb-4">
                            This project provided valuable hands-on experience in integrating mechanical, electrical, and software 
                            engineering disciplines. It taught us the importance of iterative design and real-world testing when 
                            developing autonomous systems, as well as the critical balance between algorithmic sophistication and 
                            robust, reliable operation.
                        </p>
                    </div>
                </div>
            </details>
        </section>
        
        <!-- CTA / Next Project -->
        <section>
            <div class="bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg p-8 text-center">
                <h2 class="text-2xl font-bold text-primary mb-4">Interested in more projects?</h2>
                <p class="text-lg mb-6">Check out my other engineering and robotics work</p>
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