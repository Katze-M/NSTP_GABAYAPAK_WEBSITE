<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Pending</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--bg:#f6f8fb;--card:#ffffff;--accent:#5b21b6;--muted:#6b7280}
        html,body{height:100%;margin:0;font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial; background:linear-gradient(180deg,#f8fafc 0%,var(--bg) 100%);color:#111827}
        .wrap{min-height:100%;display:flex;align-items:center;justify-content:center;padding:40px}
        .card{max-width:880px;width:100%;background:var(--card);border-radius:12px;box-shadow:0 8px 30px rgba(2,6,23,0.08);overflow:hidden;border:1px solid rgba(15,23,42,0.04);display:flex}
        .card .left{flex:1;padding:48px 40px}
        .card .right{width:320px;background:linear-gradient(180deg,rgba(91,33,182,0.06),rgba(91,33,182,0.02));padding:28px;display:flex;flex-direction:column;align-items:flex-start;justify-content:center}
        h1{font-size:32px;margin:0 0 12px;color:#0f172a}
        p.lead{margin:0 0 18px;color:#374151;font-size:16px;line-height:1.6}
        ul.steps{padding-left:18px;margin:12px 0 18px;color:var(--muted)}
        .cta{display:inline-block;padding:10px 18px;border-radius:8px;background:var(--accent);color:#fff;text-decoration:none;font-weight:600;box-shadow:0 6px 18px rgba(91,33,182,0.12)}
        .muted{color:var(--muted);font-size:14px}
        .meta{margin-top:18px}
        .icon-circle{width:56px;height:56px;border-radius:999px;background:linear-gradient(135deg,#7c3aed,#4f46e5);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;margin-bottom:8px}
        .right .small{font-size:13px;color:var(--muted)}
        @media (max-width:800px){.card{flex-direction:column}.card .right{width:100%;padding:20px}.card .left{padding:28px}}
    </style>
</head>
<body>
    @extends('layouts.app')

    @section('title', 'Registration Pending')

    @section('content')
        <div class="min-h-screen flex items-center justify-center p-6 bg-gray-50">
            <div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="md:flex">
                    <div class="md:flex-1 p-8 sm:p-10">
                        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Registration Received</h1>
                        <p class="mt-3 text-gray-600 text-base leading-relaxed">Thank you for registering. Your account has been submitted and is currently pending approval by the NSTP Program Officer.</p>

                        <p class="mt-6 text-sm text-gray-500">What happens next:</p>
                        <ul class="mt-2 ml-4 list-disc text-sm text-gray-600 space-y-2">
                            <li>The NSTP Program Officer will review your submission.</li>
                            <li>To check the status of your registration, please visit the login page and click the registration status link.</li>
                            <li>If rejected, you may re-register after addressing the remark provided by the reviewer.</li>
                        </ul>

                        <div class="mt-6 flex items-center gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg font-medium shadow-sm">Back to login</a>
                            <span class="text-sm text-gray-500">Or return to the login page to sign in later</span>
                        </div>
                    </div>

                    <div class="w-full md:w-80 bg-gradient-to-b from-blue-50 to-transparent p-6 flex flex-col items-start justify-center">
                        <div class="flex items-center justify-center w-14 h-14 rounded-full bg-gradient-to-br from-blue-800 to-blue-600 text-white text-lg font-semibold">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <div class="mt-4">
                            <div class="text-sm text-gray-500">Registration status</div>
                            <h2 class="mt-1 text-lg font-medium text-gray-900">Pending Review</h2>
                            <p class="mt-2 text-sm text-gray-500">We usually process registrations within 1–3 business days. Contact the administration for urgent concerns.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
