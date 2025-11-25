@extends('layouts.app')

@section('title', 'NSTP Project Management and Monitoring System')

@section('content')
<!-- HOME (About + Moderators) -->
<section id="home" class="bg-white rounded-2xl shadow-subtle overflow-hidden">

  @if(session('registration_status') === 'pending')
    <div class="p-4 max-w-4xl mx-auto mt-4 bg-yellow-100 text-yellow-800 rounded">Your registration is under review.</div>
  @endif

  <!-- IMAGE SLIDER -->
  <div class="relative w-full h-48 md:h-72 overflow-hidden rounded-2xl mb-6 md:mb-10">
    <div id="slider" class="flex transition-transform duration-700 ease-in-out">
      <img src="{{ asset('assets/1000036076.jpg') }}" class="w-full h-48 md:h-72 object-cover flex-shrink-0" alt="Slide 1">
      <img src="{{ asset('assets/1000036077.jpg') }}" class="w-full h-48 md:h-72 object-cover flex-shrink-0" alt="Slide 2">
      <img src="{{ asset('assets/1000036078.jpg') }}" class="w-full h-48 md:h-72 object-cover flex-shrink-0" alt="Slide 3">
      <img src="{{ asset('assets/1000036079.jpg') }}" class="w-full h-48 md:h-72 object-cover flex-shrink-0" alt="Slide 4">
    </div>

    <!-- Navigation Buttons -->
    <button id="prev" class="absolute top-1/2 left-2 md:left-3 -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-90 rounded-full p-1 md:p-2 shadow-md text-sm md:text-base">
      &#10094;
    </button>
    <button id="next" class="absolute top-1/2 right-2 md:right-3 -translate-y-1/2 bg-white bg-opacity-70 hover:bg-opacity-90 rounded-full p-1 md:p-2 shadow-md text-sm md:text-base">
      &#10095;
    </button>

    <!-- Dots Indicator -->
    <div class="absolute bottom-2 md:bottom-3 left-1/2 transform -translate-x-1/2 flex space-x-1 md:space-x-2">
      <span class="dot w-2 h-2 md:w-3 md:h-3 bg-white rounded-full opacity-70 cursor-pointer"></span>
      <span class="dot w-2 h-2 md:w-3 md:h-3 bg-white rounded-full opacity-50 cursor-pointer"></span>
      <span class="dot w-2 h-2 md:w-3 md:h-3 bg-white rounded-full opacity-50 cursor-pointer"></span>
      <span class="dot w-2 h-2 md:w-3 md:h-3 bg-white rounded-full opacity-50 cursor-pointer"></span>
    </div>
  </div>

  <!-- ABOUT SECTIONS -->
  <div class="p-4 md:p-8 space-y-8 md:space-y-12">
    <!-- About SACSI Section -->
    <article class="rounded-2xl p-6 md:p-8 lg:p-10" style="background-color: #F8E2E2;">
      <div class="flex items-center justify-center gap-4 mb-6 md:mb-8">
        <div class="w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 flex items-center justify-center">
          <img src="{{ asset('assets/SACSI_Logo.png') }}" alt="SACSI Logo" class="w-full h-full object-contain" onerror="this.style.display='none';">
        </div>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 text-center tracking-tight">About SACSI</h2>
      </div>
      <div class="max-w-5xl mx-auto">
        <p class="text-center text-gray-700 text-base md:text-lg lg:text-xl leading-relaxed md:leading-loose font-medium tracking-wide">
          The <span class="font-semibold text-gray-800">Social Awareness and Community Service Involvement (SACSI) Office</span> serves as the university's social involvement arm, linking students to communities through outreach and formation programs.
        </p>
        <p class="text-center text-gray-700 text-base md:text-lg lg:text-xl leading-relaxed md:leading-loose font-medium tracking-wide mt-4 md:mt-6">
          It complements classroom learning with real-world experiences and fosters academic excellence, socio-political maturity, and spiritual growth. Guided by its vision of forming socially aware, morally grounded, and service-oriented individuals, SACSI promotes a humane society anchored in <span class="font-semibold text-gray-800">faith, justice, compassion, and inclusivity</span>.
        </p>
        <p class="text-center text-gray-700 text-base md:text-lg lg:text-xl leading-relaxed md:leading-loose font-medium tracking-wide mt-4 md:mt-6">
          Its five guiding principles are <span class="font-semibold text-gray-800">authentic humanism, faith that does justice, simplicity of lifestyle, peace, and inter-faith dialogue</span>—emphasizing respect, equity, and understanding across all people, cultures, and religions.
        </p>
      </div>
    </article>

    <!-- About NSTP Section -->
    <article class="rounded-2xl p-6 md:p-8 lg:p-10 bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-100">
      <div class="flex items-center justify-center gap-4 mb-6 md:mb-8">
        <div class="w-10 h-10 md:w-12 md:h-12 lg:w-14 lg:h-14 flex items-center justify-center">
          <img src="{{ asset('assets/NSTP_Logo.png') }}" alt="NSTP Logo" class="w-full h-full object-contain" onerror="this.style.display='none';">
        </div>
        <h2 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 text-center tracking-tight">About NSTP</h2>
      </div>
      <div class="max-w-5xl mx-auto">
        <p class="text-center text-gray-700 text-base md:text-lg lg:text-xl leading-relaxed md:leading-loose font-medium tracking-wide">
          The <span class="font-semibold text-gray-800">National Service Training Program (NSTP)</span> is a government-mandated initiative for tertiary students that aims to develop civic responsibility and preparedness for national service.
        </p>
        <p class="text-center text-gray-700 text-base md:text-lg lg:text-xl leading-relaxed md:leading-loose font-medium tracking-wide mt-4 md:mt-6">
          It has three components: the <span class="font-semibold text-blue-700">Literacy Training Service (LTS)</span>, which equips students to teach literacy and numeracy to children, out-of-school youth, and underserved communities; the <span class="font-semibold text-blue-700">Civic Welfare Training Service (CWTS)</span>, which promotes civic consciousness, social responsibility, and community involvement; and the <span class="font-semibold text-blue-700">Reserve Officers' Training Corps (ROTC)</span>, which provides military training and national defense preparedness in partnership with the Philippine Air Force.
        </p>
      </div>
    </article>

    <!-- NSTP FORMATORS -->
    <section class="rounded-2xl p-4 md:p-6" style="background-color: #F8E2E2;">
      <div class="flex justify-between items-center mb-4 md:mb-6">
        <h3 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-800 text-center tracking-tight flex-1">NSTP Formators</h3>
        @if(auth()->check() && auth()->user()->isStaff())
          <a href="{{ route('formators.manage') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 ml-4">
            Manage Formators
          </a>
        @endif
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
        @forelse($formators as $formator)
          <div class="text-center">
            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full border-2 border-gray-400 mx-auto flex items-center justify-center bg-white overflow-hidden">
              @if (!empty($formator['image']))
                <img src="{{ $formator['image'] }}" class="w-full h-full object-cover" alt="{{ $formator['name'] }}">
              @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="1.5" class="w-8 h-8 md:w-10 md:h-10">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.485 0 4.5-2.015 4.5-4.5S14.485 3 12 3 7.5 5.015 7.5 7.5 9.515 12 12 12z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 21a7.5 7.5 0 0115 0v.75H4.5V21z" />
                </svg>
              @endif
            </div>
            <p class="mt-2 font-semibold text-xs md:text-sm">{{ $formator['name'] }}</p>
          </div>
        @empty
          <div class="col-span-full text-center py-8">
            <p class="text-gray-600">No NSTP Formators found.</p>
          </div>
        @endforelse
      </div>
    </section>
  </div>
</section>

<!-- Slider Script -->
<script>
  const slider = document.getElementById('slider');
  const slides = slider.children.length;
  const dots = document.querySelectorAll('.dot');
  let index = 0;

  function updateSlider() {
    slider.style.transform = `translateX(-${index * 100}%)`;
    dots.forEach((dot, i) => {
      dot.classList.toggle('opacity-70', i === index);
      dot.classList.toggle('opacity-50', i !== index);
    });
  }

  document.getElementById('next').onclick = () => {
    index = (index + 1) % slides;
    updateSlider();
  };

  document.getElementById('prev').onclick = () => {
    index = (index - 1 + slides) % slides;
    updateSlider();
  };

  dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
      index = i;
      updateSlider();
    });
  });

  setInterval(() => {
    index = (index + 1) % slides;
    updateSlider();
  }, 5000);
</script>
@endsection