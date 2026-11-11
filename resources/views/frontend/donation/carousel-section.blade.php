<section class="w-full bg-white pt-12">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <div class="swiper mySwiper w-full max-w-6xl mx-auto">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide relative" id="slide-0">
                <div class="absolute inset-0 bg-cover bg-center" 
                    style="background-image: url('{{ asset('frontend/assets/img/donation/slider_img1.png') }}'); z-index: 0;"></div>
                <div class="h-[465px] mt-1 absolute inset-0 bg-[#013370] opacity-[60%] z-10"></div>
                <div class="absolute top-2 right-2 bg-white text-[#013370] text-xs px-2 py-1 rounded-md z-20">2020</div>
                <div class="relative z-30 flex flex-col items-center justify-center text-center text-white text-[40px] font-bold w-full h-full">
                    <span>10</span>
                    <span>Students</span>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide relative" id="slide-1">
                <div class="absolute inset-0 bg-cover bg-center" 
                    style="background-image: url('{{ asset('frontend/assets/img/donation/slider_img1.png') }}'); z-index: 0;"></div>
                <div class="h-[465px] mt-1 absolute inset-0 bg-black opacity-[40%] z-10"></div>
                <div class="absolute top-2 right-2 bg-white text-[#013370] text-xs px-2 py-1 rounded-md z-20">2021</div>
                <div class="relative z-30 flex flex-col items-center justify-center text-center text-white text-[40px] font-bold w-full h-full">
                    <span>35</span>
                    <span>Students</span>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide relative" id="slide-2">
                <div class="absolute inset-0 bg-cover bg-center" 
                    style="background-image: url('{{ asset('frontend/assets/img/donation/slider_img1.png') }}'); z-index: 0;"></div>
                <div class=" h-[465px] mt-1 absolute inset-0 bg-gray-600 opacity-[30%] z-10"></div>
                <div class="absolute top-2 right-2 bg-white text-[#013370] text-xs px-2 py-1 rounded-md z-20">2023</div>
                <div class="relative z-30 flex flex-col items-center justify-center text-center text-white text-[40px] font-bold w-full h-full">
                    <span>153</span>
                    <span>Students</span>
                </div>
            </div>

            <!-- Slide 4 -->
            <div class="swiper-slide relative" id="slide-3">
                <div class="absolute inset-0 bg-cover bg-center" 
                    style="background-image: url('{{ asset('frontend/assets/img/donation/slider_img1.png') }}'); z-index: 0;"></div>
                <div class="h-[465px] mt-1 absolute inset-0 bg-gray-600 opacity-[30%] z-10"></div>
                <div class="absolute top-2 right-2 bg-white text-[#013370] text-xs px-2 py-1 rounded-md z-20">2024</div>
                <div class="relative z-30 flex flex-col items-center justify-center text-center text-white text-[40px] font-bold w-full h-full">
                    <span>531</span>
                    <span>Students</span>
                </div>
            </div>

            <!-- Slide 5 -->
            <div class="swiper-slide relative" id="slide-4">
                <div class="absolute inset-0 bg-cover bg-center" 
                    style="background-image: url('{{ asset('frontend/assets/img/donation/slider_img1.png') }}'); z-index: 0;"></div>
                <div class="h-[465px] mt-1 absolute inset-0 bg-black opacity-[40%] z-10"></div>
                <div class="absolute top-2 right-2 bg-white text-[#013370] text-xs px-2 py-1 rounded-md z-20">2027</div>
                <div class="relative z-30 flex flex-col items-center justify-center text-center text-white text-[40px] font-bold w-full h-full">
                    <span>2500</span>
                    <span>Students</span>
                </div>
            </div>
        </div>

      
    </div>
  <!-- Active Slide Indicator Line - Moved inside swiper container -->
  <div class="relative flex justify-center w-full  mt-[-160px]">
    <div id="base-line" class="w-[300px] h-[1px] bg-blue-300 relative">
        <div id="active-indicator" class="h-[6px] w-[50px] bg-[#013370] absolute bottom-0 left-0 transition-all duration-300 rounded"></div>
    </div>
</div>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        const swiper = new Swiper(".mySwiper", {
            effect: "coverflow",
            grabCursor: false,
            centeredSlides: true,
            slidesPerView: "auto",
            spaceBetween: 10,
            coverflowEffect: {
                rotate: 15,
                stretch: 0,
                depth: 150,
                modifier: 1,
                slideShadows: true,
            },
            loop: true,
            breakpoints: {
                640: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
            on: {
                slideChange: function() {
                    moveIndicator(this.realIndex);
                }
            }
        });

        function moveIndicator(index) {
            const indicator = document.getElementById("active-indicator");
            const stepWidth = 50; // width of each step
            const gap = 10; // optional gap between steps
            const totalMove = index * (stepWidth + gap);
            indicator.style.transform = `translateX(${totalMove}px)`;
        }

        // Init indicator on load
        document.addEventListener("DOMContentLoaded", () => {
            moveIndicator(swiper.realIndex);
        });
    </script>

    <!-- Optional Styling -->
    <style>
        .swiper-slide {
            width: 300px;
            height: 315px;
            border: 4px solid white;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .swiper-slide.swiper-slide-active {
            height: 480px;
        }

        .swiper-slide:not(.swiper-slide-active) {
            transform: scale(0.9);
        }

        

        .swiper-slide-prev.swiper-slide-active,
        .swiper-slide-next.swiper-slide-active {
            opacity: 1;
        }
    </style>
</section>