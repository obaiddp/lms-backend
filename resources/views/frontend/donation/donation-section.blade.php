<section class="relative w-full bg-white pt-12 pb-16 overflow-hidden">
    <!-- Left Hand Image -->
    <img src="{{ asset('frontend/assets/img/donation/donation-hand.png') }}"
         class="absolute top-0 left-0 w-[35%] max-w-[150px] md:max-w-none" alt="Left Hand" />

    <!-- Right Hand Image -->
    <img src="{{ asset('frontend/assets/img/donation/donation-hand2.png') }}"
         class="absolute bottom-0 right-0 w-[35%] max-w-[150px] md:max-w-none" alt="Right Hand" />

    <div class="container mx-auto text-center relative z-10 px-4">
        <!-- Donation Now Badge -->
        <button class="tag mx-auto aileron mb-4">
            <img src="{{ asset('frontend/assets/img/donation/hat-icon.png') }}" width="15" height="15" alt="icon">
            Donate Now
        </button>

        <!-- Headline and Description -->
        <h1 class="heading aileron text-3xl md:text-5xl">Support CEF</h1>
        <p class="description mx-auto aileron text-sm md:text-base">Help CEF preserve the prophetic knowledge and spread it to Millions of Muslims worldwide using the latest online education technology with your kind donation.</p>

        <!-- Donation Form -->
        <div class="form-container mx-auto w-full max-w-3xl">
            <!-- One-Time / Monthly Toggle -->
            <div class="flex flex-col sm:flex-row justify-center mb-4 aileron gap-2 sm:gap-0">
                <label class="toggle-btn w-full sm:w-auto text-center">
                    <input type="radio" name="donation_type" value="one_time" checked>
                    <span>One Time Donation</span>
                </label>
                <label class="toggle-btn w-full sm:w-auto text-center">
                    <input type="radio" name="donation_type" value="monthly">
                    <span>Every Month Donation</span>
                </label>
            </div>

            <!-- Amount and Currency Input -->
            <div class="flex flex-col sm:flex-row items-stretch border border-[#D2D9E0] rounded-lg overflow-hidden mb-4">
                <input type="text" placeholder="1000.00" class="w-full p-2 outline-none" />
                <select class="bg-[#0133701A] text-[#013370] p-3 border-t sm:border-t-0 sm:border-l border-[#D2D9E0] aileron">
                    <option>PKR</option>
                    <option>USD</option>
                    <option>EUR</option>
                </select>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2 mb-4">
                <button class="custom-btn1 aileron">PKR 10.00</button>
                <button class="custom-btn1 aileron">PKR 25.00</button>
                <button class="custom-btn1 aileron">PKR 50.00</button>
                <button class="custom-btn1 aileron">PKR 100.00</button>
                <button class="custom-btn1 aileron">PKR 250.00</button>
            </div>

            <!-- Checkbox for Transaction Fee -->
            <div class="mb-4">
                <label class="text-dark flex items-start gap-2 aileron text-sm sm:text-base">
                    <input type="checkbox" class="mt-1">
                    I'd like to help cover the transaction fees of PKR 50.00 for my donation.
                </label>
            </div>

            <!-- Phone Number and Zakat Dropdown (Responsive Grid) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <input type="text" placeholder="Phone No./WhatsApp No." class="p-3 border rounded w-full">
                <select class="p-3 border rounded aileron w-full">
                    <option>Zakat</option>
                    <option>Sadaqah</option>
                    <option>General Donation</option>
                </select>
            </div>

            <!-- WhatsApp Updates Toggle -->
            <div class="mb-4 flex flex-wrap items-center gap-2">
                <span class="text-dark font-bold aileron text-sm sm:text-base">I want to receive updates via WhatsApp?</span>
                <label class="switch">
                    <input type="checkbox">
                    <span class="slider round"></span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center sm:justify-start mb-4">
                <button class="custom-btn2 aileron w-full sm:w-auto">Donate Now</button>
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

.toggle-btn {
    border: 1px solid #E0E3E8;
    color: #9299A1;
    background-color: #E0E3E8;
    font-weight: bold;
    border-radius: 5px;
    padding: 10px 6px;
    cursor: pointer;
}

.toggle-btn:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

.toggle-btn:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.toggle-btn input[type="radio"] {
    display: none; /* Hide the default radio input */
}

/* Styling for active button when selected */
.toggle-btn input[type="radio"]:checked + span {
    background-color: white;
    font-weight: normal;
    color: #9299A1;
    border: 1px solid #E0E3E8;
    border-radius: 5px;
    padding: 10px 6px;

}

input::placeholder {
        color: #000;  /* Black text for the placeholder, matching your image */
    }

.custom-btn1 {
    background-color: #8DC03E;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 18px;
    transition: background-color 0.3s ease;
}

.custom-btn2 {
    background-color: #013370;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    font-size: 18px;
    transition: background-color 0.3s ease;
}

.form-container {
    padding: 20px;
    margin: 0 auto;
}


/* The switch - the box around the slider */
.switch {
  position: relative;
  display: inline-block;
  width: 60px;
  height: 28px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 26px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #8DC03E;
}

input:focus + .slider {
  box-shadow: 0 0 1px #8DC03E;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>