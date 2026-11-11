<section class="w-full bg-white pt-12 pb-16">
    <div class="container mx-auto text-center">
        <button class="tag mx-auto aileron">
            <img src={{asset('frontend/assets/img/donation/hat-icon.png')}} width="15" height="15" alt="icon">

            Achievements</button>
        <h1 class="heading aileron" >CEF Key Achievements</h1>

            <img src={{ asset('frontend/assets/img/donation/achievements.png') }} alt="achievements" class="w-1/2 mx-auto mb-4">
        <h2 class="heading2 aileron">CEF at a Glance</h2>
        <p class="date aileron">April 204 - November 2024</p>
        <p class="description mx-auto aileron">A Global online Islamic institute, providing universal access to the world’s best Islamic education, 
            collaborating with leading Islamic experts and evolving Islamic institutions.</p>

            <div class="relative flex justify-center w-full mt-8">
                <div id="base-line" class="w-[300px] h-[1px] bg-blue-300 relative">
                    <div id="active-indicator" class="h-[6px] w-[50px] bg-[#013370] absolute bottom-0 left-0 transition-all duration-300 rounded"></div>
                </div>
            </div>
        </div>
</section>


<style>
    .tag {
      display: flex;
        align-items: center;
        gap: 5px;
      background-color: rgba(1, 51, 112, 0.1);
      color: #013370;
      font-size: 16px;
      font-weight: 600;
      padding: 5px 10px;
      border-radius: 15px;
      margin-bottom: 10px;
    }


.heading {
    color: #000000;
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 10px;
}
.heading2 {
    color: #013370;
    font-size: 40px;
    font-weight: bold;
}
.date {
    color: #013370;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
}
.description {
    font-size: 16px;
    line-height: 1.5;
    color: #4A4A4A;
    margin-bottom: 20px;
    max-width: 700px !important;
}
</style>