<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Models\Banner;
use App\Models\ClinicSetting;
use App\Models\ContactMessage;
use App\Models\Dentist;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Service;
use App\Models\SiteSection;
use App\Models\SmileCase;
use App\Models\Testimonial;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Render the clinic public landing page and booking system.
     */
    public function index(): View
    {
        $clinic = $this->getClinicSetting();
        $banner = Banner::where('is_active', true)->orderBy('order')->first();
        $services = Service::where('is_active', true)->with('dentists')->orderBy('order')->get();
        $dentists = Dentist::where('is_active', true)->with('services')->orderBy('order')->get();
        $smileCases = SmileCase::where('is_active', true)->with(['service', 'dentist'])->orderBy('order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('order')->get();
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();
        $posts = Post::where('is_published', true)->orderByDesc('published_at')->take(3)->get();
        $sections = SiteSection::forPage('home')->get()->keyBy('section_key');

        return view('welcome', compact(
            'clinic',
            'banner',
            'services',
            'dentists',
            'smileCases',
            'testimonials',
            'faqs',
            'posts',
            'sections'
        ));
    }

    /**
     * Render the dedicated clinic About & Specialists page.
     */
    public function about(): View
    {
        $clinic = $this->getClinicSetting();
        $dentists = Dentist::where('is_active', true)->with('services')->orderBy('order')->get();
        $testimonials = Testimonial::where('is_active', true)->orderBy('order')->get();
        $sections = SiteSection::forPage('about')->get()->keyBy('section_key');

        return view('about', compact(
            'clinic',
            'dentists',
            'testimonials',
            'sections'
        ));
    }

    /**
     * Render the comprehensive Services & Treatments catalog page.
     */
    public function services(): View
    {
        $clinic = $this->getClinicSetting();
        $services = Service::where('is_active', true)->with('dentists')->orderBy('order')->get();
        $dentists = Dentist::where('is_active', true)->get();
        $sections = SiteSection::forPage('services')->get()->keyBy('section_key');

        return view('services.index', compact(
            'clinic',
            'services',
            'dentists',
            'sections'
        ));
    }

    /**
     * Render a single Service procedure detail page.
     */
    public function serviceDetail(string $slug): View
    {
        $clinic = $this->getClinicSetting();
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with(['dentists', 'smileCases'])
            ->firstOrFail();

        $relatedServices = Service::where('is_active', true)
            ->where('id', '!=', $service->id)
            ->take(3)
            ->get();

        return view('services.show', compact(
            'clinic',
            'service',
            'relatedServices'
        ));
    }

    /**
     * Render the dedicated Contact page.
     */
    public function contact(): View
    {
        $clinic = $this->getClinicSetting();
        $faqs = Faq::where('is_active', true)->orderBy('order')->take(5)->get();
        $sections = SiteSection::forPage('contact')->get()->keyBy('section_key');

        return view('contact', compact(
            'clinic',
            'faqs',
            'sections'
        ));
    }

    /**
     * Handle incoming contact form submissions.
     */
    public function contactSubmit(StoreContactMessageRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();
        $message = ContactMessage::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => '¡Tu mensaje ha sido enviado con éxito! Nuestro equipo se pondrá en contacto contigo en breve.',
                'data' => [
                    'id' => $message->id,
                    'name' => $message->name,
                ],
            ]);
        }

        return back()->with('success', '¡Tu mensaje ha sido enviado con éxito! Nuestro equipo se pondrá en contacto contigo en breve.');
    }

    /**
     * Render the dedicated Blog articles index page.
     */
    public function blog(): View
    {
        $clinic = $this->getClinicSetting();
        $posts = Post::where('is_published', true)->orderByDesc('published_at')->get();
        $categories = Post::where('is_published', true)
            ->pluck('category')
            ->filter()
            ->unique()
            ->values();
        $sections = SiteSection::forPage('blog')->get()->keyBy('section_key');

        return view('blog.index', compact(
            'clinic',
            'posts',
            'categories',
            'sections'
        ));
    }

    /**
     * Render a single Blog article detail page.
     */
    public function blogPost(string $slug): View
    {
        $clinic = $this->getClinicSetting();
        $post = Post::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedPosts = Post::where('is_published', true)
            ->where('id', '!=', $post->id)
            ->take(3)
            ->get();

        return view('blog.show', compact(
            'clinic',
            'post',
            'relatedPosts'
        ));
    }

    /**
     * Render the clinic Privacy Policy page.
     */
    public function privacy(): View
    {
        $clinic = $this->getClinicSetting();

        return view('legal.privacy', compact('clinic'));
    }

    /**
     * Render the clinic Terms and Conditions page.
     */
    public function terms(): View
    {
        $clinic = $this->getClinicSetting();

        return view('legal.terms', compact('clinic'));
    }

    /**
     * Render the clinic Patient Rights and Responsibilities page.
     */
    public function patientRights(): View
    {
        $clinic = $this->getClinicSetting();

        return view('legal.patient-rights', compact('clinic'));
    }

    /**
     * Helper to get active clinic setting with fallbacks.
     */
    private function getClinicSetting(): ClinicSetting
    {
        return ClinicSetting::first() ?? new ClinicSetting([
            'clinic_name' => 'Aura Dental Studio',
            'tagline' => 'Odontología moderna, integral y sin dolor para sonrisas radiantes.',
            'phone' => '+1 (555) 234-5678',
            'whatsapp' => '+15552345678',
            'email' => 'hola@auradental.com',
            'address' => 'Av. Las Palmas 742, Piso 4, Distrito Salud',
            'emergency_phone' => '+1 (555) 911-DENT',
            'opening_hours' => "Lunes a Viernes: 8:00 AM - 7:00 PM\nSábado: 9:00 AM - 3:00 PM\nDomingo: Solo Urgencias",
        ]);
    }
}
