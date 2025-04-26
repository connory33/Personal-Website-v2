<!doctype html>
<html lang="en" class="h-full">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Connor Young's personal website" />
    <meta name="author" content="Connor Young" />
    <link rel="icon" href="../../../../favicon.ico" />
    <title>Connor Young</title>

    <link rel="stylesheet" href="../resources/css/default_v3.css" />
    <script src="https://cdn.tailwindcss.com"></script>
  </head>

  <body class="flex flex-col min-h-screen bg-white text-gray-900">
    <!-- Header -->
    <?php include 'header.php'; ?>

    <!-- Main -->
    <main class="flex-grow">
      <!-- <section class="text-center bg-cover bg-center py-16" style="background-image: url('https://picsum.photos/1200/800')"> -->
        <section class="text-center bg-[url('../resources/images/enchantedlake.jpg')] bg-cover bg-center py-16">

          <h1 class="text-6xl font-bold" style="color: #18314f">Connor Young</h1>
          <p class="mt-6">
            <a
              href="resources/ConnorYoung_Resume_Apr2025.pdf"
              download
              class="inline-block px-6 py-3 bg-blue-600 text-white font-semibold rounded shadow hover:bg-blue-700 transition"
            >
              Download Resume
            </a>
          </p>
</section>

      <section class="text-center my-12 px-4">
        <h4 class="text-3xl md:text-4xl font-medium mb-4">
          <a href="nhlIndex.html" class="text-blue-700 hover:underline">Check out my latest/active project here!</a>
        </h4>
        <hr class="border-blue-900 w-4/5 mx-auto mb-8" />
        <h2 class="text-4xl font-semibold mb-10">Past Projects</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
          
          <div class="bg-white border shadow rounded-lg p-6 text-left">
            <h3 class="text-xl font-bold mb-2">NHL Line Analysis</h3>
            <p class="text-sm text-gray-600 mb-4">This was an analysis and investigation into the usage and dependency of different NHL teams on their various "lines" of forwards.</p>
            <a href="nhlLinesProject.html" class="text-blue-600 hover:underline">View Project →</a>
          </div>
        

            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">NBA Fantasy Projection</h3>
              <p class="text-sm text-gray-600 mb-4">This project compared the efficacy of various machine learning models in predicting fantasy scores for NBA players.</p>
              <a href="nbaFantasyProjections.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>
  

            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">NFL Roster Optimizer</h3>
              <p class="text-sm text-gray-600 mb-4">This was a fun exercise in roster optimization using Madden ratings and Excel's evolutionary solver.</p>
              <a href="maddenOptimizer.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>


            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">Senior Design Project</h3>
              <p class="text-sm text-gray-600 mb-4">Check back for a new project coming soon!</p>
              <a href="seniorDesignProject.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>
      

            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">Autonomous Robot</h3>
              <p class="text-sm text-gray-600 mb-4">This project used circuits, Arduino, and bit-level C code to build an autonomous robot for a cube-clearing competition.</p>
              <a href="autonomousRobot.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>
        

            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">Thermistor Cleaner</h3>
              <p class="text-sm text-gray-600 mb-4">This partner project involved creating a commercial-ready cleaning device using rapid prototyping techniques.</p>
              <a href="thermistorCleaner.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>
        

            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">Water Pump</h3>
              <p class="text-sm text-gray-600 mb-4">This group project involved design and manufacturing of a mechanical pump capable of moving 1L of water per minute.</p>
              <a href="waterPump.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>


            <div class="bg-white border shadow rounded-lg p-6 text-left">
              <h3 class="text-xl font-bold mb-2">Planter Boxes</h3>
              <p class="text-sm text-gray-600 mb-4">This was my Eagle Scout Project for the Menlo Park VA Hospital. I led 10+ scouts for 100+ total man-hours.</p>
              <a href="planterBoxes.html" class="text-blue-600 hover:underline">View Project →</a>
            </div>
        
          </div>
      </section>
    </main>

    <!-- Footer -->
    <!-- <footer class="text-white text-center py-4" style="background-color: #18314f">
      <div class="max-w-7xl mx-auto px-4 flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
        <p>&copy; 2025 Connor Young</p>
        <p><a href="https://www.linkedin.com/in/connoryoung33/" class="hover:underline">LinkedIn</a></p>
        <p><a href="https://www.github.com/connory33" class="hover:underline">GitHub</a></p>
        <p><a href="mailto:connor@connoryoung.com" class="hover:underline">connor@connoryoung.com</a></p>
      </div>
    </footer> -->
    <?php


    <?php include 'footer.php'; ?>


    
  </body>
</html>

