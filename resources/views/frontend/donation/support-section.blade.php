<section class="w-full bg-white pt-12 pb-16">
    <div class="container mx-auto text-center">
        <button class="tag mx-auto aileron">
            <img src={{asset('frontend/assets/img/donation/hat-icon.png')}} width="15" height="15" alt="icon">
Why Support CEF</button>
        <h1 class="heading aileron">CEF is a not-for-profit organization</h1>
        <p class="description mx-auto aileron">CEF is a not-for-profit, community-based organization. As part of our mission to achieve our aims, in the past year we:</p>

            <!-- First Row - 2 columns -->
            <div class="row-new row-2cols">
                <div class="stats-circle" style="margin-right: 40px;">
                    <div class="stats-content">
                        <div class="stats-value aileron">7k+</div>
                        <div class="stats-label aileron">Trained Students</div>
                    </div>
                </div>
                <div class="stats-circle" style="margin-left: 40px;">
                    <div class="stats-content">
                        <div class="stats-value aileron">750+</div>
                        <div class="stats-label aileron">Scholarships</div>
                    </div>
                </div>
            </div>
            
            <!-- Second Row - 3 columns -->
            <div class="row row-3cols">
                <div class="stats-circle" style="margin-right: 40px;">
                    <div class="stats-content">
                        <div class="stats-value aileron">120+</div>
                        <div class="stats-label aileron">Courses</div>
                    </div>
                </div>
                
                <div class="image-circle">
                    <img src="https://cefonlineacademy.com/uploads/setting/1717146464-mXKw2tkVQh.png" alt="profile">
                </div>
                
                <div class="stats-circle" style="margin-left: 40px;">
                    <div class="stats-content">
                        <div class="stats-value aileron">5K+</div>
                        <div class="stats-label aileron">Sessions</div>
                    </div>
                </div>
            </div>

            <div class="row-2cols-new">
                <div class="stats-circle" style="margin-right: 40px;">
                    <div class="stats-content" >
                        <div class="stats-value aileron">150+</div>
                        <div class="stats-label aileron">Countries</div>
                    </div>
                </div>
                <div class="stats-circle" style="margin-left: 40px;">
                    <div class="stats-content">
                        <div class="stats-value aileron">150+</div>
                        <div class="stats-label aileron">Scholars</div>
                    </div>
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

.description {
    font-size: 16px;
    line-height: 1.5;
    color: #4A4A4A;
    margin-bottom: 20px;
    max-width: 600px !important;
}


        .row-2cols {
            display: flex !important;
            justify-content: center !important;
            position: relative;
            gap: 60px !important;

        }
    .row-3cols {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
}
.row-2cols-new {
            display: flex !important;
            justify-content: center !important;
            position: relative;
            gap: 60px !important;

        }
        /* Stats Circle Styles */
        .stats-circle {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stats-circle::before {
            content: '';
            position: absolute;
            width: calc(100% - 8px);
            height: calc(100% - 8px);
            border-radius: 50%;
            border-left: 10px solid #013370;
            border-right: 5px solid #e0e0e0;
            border-top: 10px solid #013370;
            border-bottom: 5px solid #e0e0e0;
            background: none;
        }
        .stats-content {
            position: relative;
            text-align: center;
            z-index: 1;
        }
        .stats-label {
            font-size: 16px;
            color: #013370;
        }
        .stats-value {
            font-size: 28px;
            font-weight: bold;
            color: #013370;
        }
        
        /* Middle Image Circle */
        .image-circle {
    width: 200px;
    height: 200px;
    border: 2px dashed #74B945;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    background: white;
    position: relative;
    z-index: 2;
    margin-top: -50px; /* Adjust the overlap */
    flex-shrink: 0;
}
        .image-circle img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            margin-left: 4px !important;
        }
 /* Responsive Styles */
 @media (max-width: 768px) {
    .heading {
      font-size: 32px;
    }

    .description {
      font-size: 14px;
    }

    .tag {
      font-size: 14px;
      padding: 4px 8px;
    }

    .row-2cols,
    .row-3cols,
    .row-2cols-new {
      flex-direction: column;
      align-items: center;
      gap: 30px;
    }

    .stats-circle,
    .image-circle {
      width: 140px;
      height: 140px;
    }

    .stats-value {
      font-size: 22px;
    }

    .stats-label {
      font-size: 14px;
    }

    .image-circle {
      margin-top: 0;
    }
  }
</style>