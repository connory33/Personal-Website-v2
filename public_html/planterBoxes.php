<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Wheelchair-accessible planter boxes designed and built for veterans at the Menlo Park VA as an Eagle Scout project">
    <meta name="author" content="Connor Young">
    <link rel="icon" href="../resources/images/favicon.ico">

    <title>Connor Young | Wheelchair-Accessible Planter Boxes</title>

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
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4 animate-fade-in">Wheelchair-Accessible Planter Boxes</h1>
            <p class="text-lg text-slate-100 mb-8 animate-fade-in" style="animation-delay: 0.1s;">
                Eagle Scout project supporting veterans at Menlo Park VA
            </p>
            <div class="flex flex-wrap gap-4 animate-fade-in" style="animation-delay: 0.2s;">
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Community Service</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Eagle Scout Project</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Accessible Design</span>
                <span class="bg-white/20 text-white px-3 py-1 rounded-full text-sm">Woodworking</span>
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
                    For my Eagle Scout project, I designed and built wheelchair-accessible planter boxes that enable 
                    mobility-impaired veterans at the Menlo Park VA to continue enjoying gardening despite physical limitations.
                </p>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6 my-8">
                    <!-- <h3 class="text-xl font-semibold mb-3 text-primary">Project Impact</h3> -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-center">
                        <div>
                            <div class="text-3xl font-bold text-primary mb-2">3</div>
                            <p class="text-slate-600">Accessible planter boxes built</p>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-primary mb-2">90</div>
                            <p class="text-slate-600">Total scout-hours worked</p>
                        </div>
                        <!-- <div>
                            <div class="text-3xl font-bold text-primary mb-2">100%</div>
                            <p class="text-slate-600">Wheelchair accessible design</p>
                        </div> -->
                    </div>
                </div>
                
                <p class="mb-4">
                    After approaching the Menlo Park VA to inquire about service opportunities that would benefit veterans, 
                    I learned that many residents enjoy gardening but face accessibility challenges due to mobility limitations. 
                    This inspired me to create customized planter boxes that would allow wheelchair-bound veterans to 
                    continue engaging in a therapeutic activity they love.
                </p>
                
                <p class="mb-4">
                    The project combined my passion for woodworking with my desire to serve veterans, while also fulfilling 
                    the leadership and community service requirements for the Eagle Scout rank. Beyond the technical skills 
                    involved, the project provided valuable experience in project management, team leadership, and community engagement.
                </p>
            </div>
        </section>

        <!-- Project Gallery Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Project Gallery</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/planter4.JPG" alt="Planter Box Construction">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Initial Construction</h3>
                        <p class="text-slate-600">Building the redwood frame structure that forms the basis of the planter box.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/plantercaulk.png" alt="Sealing the Interior">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Sealing Process</h3>
                        <p class="text-slate-600">Applying waterproof caulk to seal all interior seams before installing the plastic liner.</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/planterplastic.png" alt="Installing Plastic Liner">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Waterproof Lining</h3>
                        <p class="text-slate-600">Installing the plastic liner to protect the wood and create a durable planting area.</p>
                    </div>
                </div>
                
                <div class="image-card bg-white rounded-lg overflow-hidden shadow-sm border border-slate-200">
                    <img class="w-full h-64 object-cover object-center" src="../resources/images/planterfinished.JPG" alt="Completed Planter Box">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg mb-2">Finished Product</h3>
                        <p class="text-slate-600">The completed wheelchair-accessible planter box ready for delivery to the VA facility.</p>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Design & Construction Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Design & Construction Process</h2>
            
            <div class="space-y-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Needs Assessment & Design</h3>
                    <p class="mb-4">
                        After identifying the need for accessible gardening options, I conducted research on wheelchair-accessible 
                        designs and consulted with VA staff to ensure the planter boxes would meet the specific needs of their residents.
                    </p>
                    <p class="mb-4">
                        Key design considerations included:
                    </p>
                    <ul class="space-y-2 list-disc pl-5">
                        <li><span class="font-medium">Height:</span> Positioned at an optimal level for wheelchair users</li>
                        <li><span class="font-medium">Clearance:</span> Designed with knee space underneath to allow close access</li>
                        <li><span class="font-medium">Mobility:</span> Added wheels for staff to reposition as needed</li>
                        <li><span class="font-medium">Drainage:</span> Incorporated proper drainage system to prevent overwatering</li>
                        <li><span class="font-medium">Durability:</span> Selected materials for long-term outdoor use</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Materials Selection</h3>
                    <p class="mb-4">
                        I carefully selected materials that balanced durability, cost-effectiveness, and functionality:
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium mb-2">Primary Materials</h4>
                            <ul class="space-y-2 list-disc pl-5">
                                <li><span class="font-medium">Redwood:</span> Naturally resistant to rot and insects, perfect for outdoor use</li>
                                <li><span class="font-medium">Plywood:</span> Used for the bottom panel to provide structural support while reducing cost</li>
                                <li><span class="font-medium">Waterproof Caulk:</span> Applied to seal the interior seams</li>
                                <li><span class="font-medium">Plastic Liner:</span> Installed to protect the wood and extend the life of the planters</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-medium mb-2">Hardware & Accessories</h4>
                            <ul class="space-y-2 list-disc pl-5">
                                <li><span class="font-medium">Drainage Spigots:</span> Installed to prevent water accumulation</li>
                                <li><span class="font-medium">Caster Wheels:</span> Added for mobility and repositioning</li>
                                <li><span class="font-medium">Weather-Resistant Screws:</span> Used for construction to prevent rusting</li>
                                <li><span class="font-medium">Wood Stain:</span> Applied to enhance appearance and protection</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Construction Process</h3>
                    <p class="mb-4">
                        The construction process involved multiple stages and required coordinating a team of volunteers:
                    </p>
                    <ol class="space-y-3 list-decimal pl-5">
                        <li><span class="font-medium">Material Preparation:</span> Cutting redwood boards and plywood to specified dimensions</li>
                        <li><span class="font-medium">Assembly:</span> Constructing the frame and attaching the plywood base</li>
                        <li><span class="font-medium">Waterproofing:</span> Applying caulk to seal all interior seams</li>
                        <li><span class="font-medium">Drainage Installation:</span> Installing plastic liner and drainage spigots</li>
                        <li><span class="font-medium">Finishing:</span> Sanding all surfaces and applying weather-resistant stain</li>
                        <li><span class="font-medium">Mobility Enhancement:</span> Attaching caster wheels to the base</li>
                        <li><span class="font-medium">Quality Control:</span> Inspecting each planter for structural integrity and functionality</li>
                    </ol>
                </div>
            </div>
        </section>
        
    
        
        <!-- Leadership & Impact Section -->
        <section class="mb-16">
            <h2 class="text-2xl font-bold text-primary mb-6 pb-2 border-b border-slate-200">Leadership & Impact</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Project Leadership</h3>
                    <p class="mb-4">
                        As project leader, I was responsible for:
                    </p>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Developing the project plan and timeline</li>
                        <li>Coordinating with the Menlo Park VA facility</li>
                        <li>Creating detailed material lists and budgets</li>
                        <li>Recruiting and managing volunteer teams</li>
                        <li>Ensuring safety protocols were followed during construction</li>
                        <li>Documenting the project's progress</li>
                        <li>Delivering the completed planter boxes</li>
                    </ul>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                    <h3 class="text-xl font-semibold mb-3 text-primary">Community Impact</h3>
                    <p class="mb-4">
                        The wheelchair-accessible planter boxes have made a meaningful difference:
                    </p>
                    <ul class="space-y-2 list-disc pl-5">
                        <li>Enabled wheelchair-bound veterans to continue gardening</li>
                        <li>Provided therapeutic activity that contributes to mental well-being</li>
                        <li>Created opportunities for social interaction among veterans</li>
                        <li>Demonstrated community support for those who served our country</li>
                    </ul>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
                <h3 class="text-xl font-semibold mb-4 text-primary">Personal Growth & Reflection</h3>
                <p class="mb-4">
                    This Eagle Scout project was a transformative experience that helped me develop valuable skills and perspectives:
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <ul class="space-y-2 list-disc pl-5">
                        <li><span class="font-medium">Project Management:</span> Coordinating complex tasks across several months</li>
                        <li><span class="font-medium">Leadership:</span> Guiding volunteer teams toward a common goal</li>
                        <li><span class="font-medium">Problem-Solving:</span> Adapting designs to meet specific accessibility needs</li>
                    </ul>
                    <ul class="space-y-2 list-disc pl-5">
                        <li><span class="font-medium">Communication:</span> Working with facility staff and project volunteers</li>
                        <li><span class="font-medium">Technical Skills:</span> Refining woodworking and construction abilities</li>
                        <li><span class="font-medium">Community Awareness:</span> Understanding the needs of veterans and people with disabilities</li>
                    </ul>
                </div>
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