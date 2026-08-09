@extends('layouts.appfront')

@section('title', 'Peoples Foundation | Scholarship')
<style type="text/css">
p {
    margin: 0 0 10px;
    font-size: 16px;
    text-align: justify;
}
ul > li {
    font-size: 15px;
    text-align:left;
}
</style>
@section('content_header')
  <h1 style="line-height:60px" class="text-center">
    EDUCATIONAL SCHOLARSHIP
  </h1>
@stop

@section('content')
         <!-- Default box -->
               <div class="row">
                  <div class="col-md-8 col-md-offset-2">
                        <div>

<p>Educated generation is the strength of any nation. People’s Foundation proposes scholarship for the
advancement of Kerala society in the field of education. Trough the scholarship scheme , People’s
Foundation intended to develop individuals with high moral values, assist academically talented
students from financially poor background to pursue higher education in selected fields , provide
scholarship to students to join nationally reputed educational institutions and gain employment
opportunities , provide assistance to students from poor families to join short-term employment training
courses, and also chalk out long term plans to identify talented students and motivate them to pursue
advanced courses.</p>

<h3>Field of scholarship</h3>
<p>Scholarship will be provided for regular courses in media studies, management studies, social sciences
and legal studies.</p>

<p>Orientation and entrance examination coaching for nationally reputed services like civil services, UGC
(NET-JRF) exams, Indian Economic services, Indian engineering services, Indian statistical services and
nationally reputed institutions like IIM, IIT, AISER, IIS, NIT, AIIMS and central universities.
</p>
<ul>
<li>Scholarship for financially weak students studying in reputed central universities located in</li>
various parts of the country
<li>Scholarship for financially weak students for short-term employment training courses</li>
<li>Special assistance to extremely poor students to help them continue education</li>
<li>Study kits for school students from poor and backward areas in the beginning of academic year</li>
</ul>
<h3>Procedures</h3>
<ul>
<li><b>Register and fill required data in the appropriate fields in the e-application form</b></li>
<li><b>After filling the data take a print out of the application</b></li>
<li><b>Get signature and stamp from the head of the institution/department</b></li>
<li><b>Attach mark list / certificate of qualifying course, adhar card</b></li>
<li><b>Pass the hard copy to people’s Foundation local/area representatives</b></li>
<li><b>The applicant will be called for an interview to assess</b></li>
</ul>
                        </div>
                        <h3>HIGHER EDUCATION SCHOLARSHIP  {{ $yearsetting->yearperiod ?? null }}</h3>
                    @if ($yearsetting->entryenable && $yearsetting->applimit > $yearsetting->getApplicationsCount() )
                        <div class="label bg-olive btn-flat margin" style="font-size:16px;">Online Application Registration is open Now!!</div>

                        {{-- Applications are taken in the People's Foundation beneficiary portal,
                             which covers every scheme rather than scholarships alone, and runs the
                             whole workflow from submission through to disbursement. The guide below
                             walks the applicant through that portal before they leave this page. --}}
                        @include('partials.portal-application-guide')

                        <a class="btn bg-orange btn-flat margin" href="{{ config('services.portal.url') }}" target="_blank" rel="noopener">Apply Now</a>
                    @else
                        <div class="label status bg-red" style="font-size:16px;">Applications to the scholarship scheme reached the limit for this month!!</div>
                    @endif

                  </div>
              </div>
@stop
