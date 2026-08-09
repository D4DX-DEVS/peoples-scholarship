{{--
    Step-by-step walkthrough of the beneficiary portal, shown on the landing page
    just above the Apply Now button. The screenshots were captured from the live
    portal and live in public/images/portal-guide.
--}}
@push('css')
<style type="text/css">
    .apply-guide {
        margin: 25px 0 30px;
        text-align: left;
    }
    .apply-guide > h3 {
        margin-bottom: 5px;
    }
    .apply-guide .apply-guide-intro {
        font-size: 15px;
        color: #555;
        margin-bottom: 20px;
    }
    .apply-guide-step {
        border: 1px solid #e2e2e2;
        border-left: 4px solid #f39c12;
        border-radius: 3px;
        padding: 15px 18px;
        margin-bottom: 18px;
        background: #fff;
    }
    .apply-guide-step h4 {
        margin: 0 0 8px;
        font-size: 17px;
        font-weight: bold;
        line-height: 1.4;
    }
    .apply-guide-step .apply-guide-num {
        display: inline-block;
        min-width: 26px;
        height: 26px;
        line-height: 26px;
        margin-right: 8px;
        text-align: center;
        border-radius: 50%;
        background: #f39c12;
        color: #fff;
        font-size: 14px;
    }
    .apply-guide-step p,
    .apply-guide-step li {
        font-size: 15px;
        text-align: left;
    }
    .apply-guide-step ul {
        padding-left: 20px;
        margin-bottom: 10px;
    }
    .apply-guide-shot {
        display: block;
        width: 100%;
        max-width: 100%;
        height: auto;
        margin-top: 10px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .apply-guide-note {
        border: 1px solid #f0c36d;
        background: #fdf6e3;
        border-radius: 3px;
        padding: 12px 15px;
        margin-bottom: 18px;
        font-size: 15px;
    }
    .apply-guide-ready {
        font-size: 15px;
        margin-bottom: 10px;
    }
</style>
@endpush

<div class="apply-guide">
    <h3>How to apply — step by step</h3>
    <p class="apply-guide-intro">
        Applications are submitted in the People&rsquo;s Foundation beneficiary portal, which
        opens in a new tab when you press <b>Apply Now</b>. The portal carries every scheme of
        the Foundation, so please follow the steps below and make sure you select the
        <b>Higher Education Scholarship</b> scheme. Keep your mobile phone with you &mdash; login
        works through an OTP sent to your number.
    </p>

    <div class="apply-guide-note">
        <b>Keep ready before you start:</b> a working mobile number (WhatsApp preferred),
        your date of birth and address, details of your previous examinations (institution,
        year of passing, grade/CGPA), the course and fee details of your present study, family
        income details, and scanned copies (PDF or image) of your SSLC certificate, Plus Two
        certificate, degree certificate, bank passbook, Aadhaar card and a recommendation
        letter from the head of your institution.
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">1</span> Choose &ldquo;Beneficiary Login&rdquo;</h4>
        <p>
            The portal opens on the <b>Select Login Type</b> screen. Choose <b>Beneficiary Login</b> —
            this is the option for applicants. Do not choose Admin Login, which is meant for
            Foundation staff. Press <b>Continue</b>.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/01-login-type.jpg') }}"
             alt="Beneficiary Login selected on the Select Login Type screen">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">2</span> Enter your 10-digit mobile number</h4>
        <p>
            There is no username or password. Type the mobile number you want to register with —
            all further communication about your application goes to this number, so use one that
            stays with you. Press <b>Send OTP</b>.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/02-mobile-number.jpg') }}"
             alt="Mobile number entry screen of the beneficiary portal">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">3</span> Verify the OTP</h4>
        <p>
            Enter the six-digit code received on your phone and press <b>Verify &amp; Login</b>.
            If the message does not arrive, wait a few seconds and use <b>Resend OTP</b>.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/03-otp.jpg') }}"
             alt="OTP verification screen of the beneficiary portal">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">4</span> Complete your profile (first-time sign-up)</h4>
        <p>
            The first time you log in, the portal asks you to complete your profile. This is the
            sign-up step — the application form will not open until it is saved. Fill in:
        </p>
        <ul>
            <li><b>Full Name</b> — as it appears in your certificates</li>
            <li><b>Gender</b></li>
            <li><b>District</b>, then <b>Area</b>, then <b>Unit</b> — choose them in that order, because the
                Area list loads after the District is picked and the Unit list after the Area</li>
        </ul>
        <p>
            Your mobile number is already filled in and cannot be changed. Press <b>Save Profile</b>.
            You can come back later through the <b>Profile</b> link at the top of the portal to correct
            these details.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/04-profile.jpg') }}"
             alt="Beneficiary profile form with name, gender, district, area and unit">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">5</span> Open &ldquo;Browse Schemes&rdquo;</h4>
        <p>
            After saving the profile you reach your dashboard. Open the <b>Browse Schemes</b> tab to
            see every scheme that is currently open. Each card shows the benefit amount, the closing
            date and the key eligibility conditions.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/05-browse-schemes.jpg') }}"
             alt="Browse Schemes tab listing all open schemes">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">6</span> Select the Higher Education Scholarship — and only that</h4>
        <p>
            The portal also lists livelihood, housing and other schemes. For the educational
            scholarship, filter by the <b>Education</b> category and press <b>Apply Now</b> on the
            <b>Higher Education Scholarship</b> card. Applications sent to any other scheme will not be
            treated as scholarship applications.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/06-select-scholarship.jpg') }}"
             alt="Higher Education Scholarship card under the Education category">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">7</span> Fill the five pages of the application</h4>
        <p>
            The scholarship form runs over five pages, with a progress bar at the top:
        </p>
        <ul>
            <li><b>Page 1 &mdash; Personal details:</b> name, address, pincode, WhatsApp number, date of
                birth, age, gender, religion</li>
            <li><b>Page 2 &mdash; Educational details:</b> your examinations with the institution, year of
                result, grade/CGPA and back papers, if any</li>
            <li><b>Page 3 &mdash; Fee details:</b> tuition fee and hostel fee per year, and the total</li>
            <li><b>Page 4 &mdash; Family details:</b> family annual income and details of your house</li>
            <li><b>Page 5 &mdash; Documents and declaration</b></li>
        </ul>
        <p>
            Use <b>Next</b> to move ahead and <b>Previous</b> to go back and correct an entry. <b>Save for
            Later</b> stores the form as a draft, so you can close the portal and continue from where
            you stopped.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/07-application-form.jpg') }}"
             alt="Page 1 of the Higher Education Scholarship application form">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">8</span> Upload the documents and submit</h4>
        <p>
            On the last page attach the SSLC certificate, Plus Two certificate, degree certificate,
            a copy of the candidate&rsquo;s bank passbook, the candidate&rsquo;s Aadhaar copy and the
            recommendation letter from the head of the institution. Each file is uploaded as soon as
            you choose it, and the file name appears next to the heading. Tick the declaration and
            press <b>Submit Application</b>.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/08-documents.jpg') }}"
             alt="Document upload page with the declaration and Submit Application button">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">9</span> Note down your application number</h4>
        <p>
            On submission the portal shows a confirmation with your application number (for example
            APP2026000242). Save this number — you will be asked for it in all correspondence about
            your application.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/09-submitted.jpg') }}"
             alt="Application submitted confirmation showing the application number">
    </div>

    <div class="apply-guide-step">
        <h4><span class="apply-guide-num">10</span> Track your application</h4>
        <p>
            Log in again with the same mobile number at any time and open <b>My Applications</b> to see
            the stage your application has reached — Submitted, Review, Committee or Approved. Shortlisted
            applicants are called for an interview, so keep your phone reachable.
        </p>
        <img class="apply-guide-shot" src="{{ asset('images/portal-guide/10-track-status.jpg') }}"
             alt="My Applications list showing the submitted scholarship application and its status">
    </div>

    <p class="apply-guide-ready">
        Ready? Press <b>Apply Now</b> below to open the beneficiary portal in a new tab.
    </p>
</div>
