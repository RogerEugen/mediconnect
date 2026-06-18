<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-teal-600">Anonymized consultation</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 dark:text-white">Start a Clinical Discussion</h1>
            <p class="mt-1 text-sm text-slate-500">Share only the minimum clinical information required for professional discussion.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-rose-900">
                <div class="flex gap-3">
                    <div class="text-xl">🔒</div>
                    <div>
                        <h2 class="font-bold">Patient privacy is mandatory</h2>
                        <p class="mt-1 text-sm leading-6 text-rose-800">Do not include names, phone numbers, addresses, national IDs, hospital numbers, exact birth dates, identifiable photographs, or any unique detail that can reveal the patient.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('clinical-cases.store') }}" class="space-y-6">
                @csrf

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-teal-600">1. Discussion context</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-900 dark:text-white">What does the team need to understand?</h2>
                    </div>
                    <div class="space-y-5">
                        <div>
                            <label for="title" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Case title</label>
                            <input id="title" name="title" value="{{ old('title') }}" required maxlength="255"
                                   placeholder="e.g. Persistent hypoxaemia with inconclusive imaging"
                                   class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                            @error('title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div>
                                <label for="specialization_id" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Relevant specialty</label>
                                <select id="specialization_id" name="specialization_id" class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    <option value="">Multidisciplinary / unsure</option>
                                    @foreach($specializations as $specialization)
                                        <option value="{{ $specialization->id }}" @selected(old('specialization_id') == $specialization->id)>{{ $specialization->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="urgency" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Discussion priority</label>
                                <select id="urgency" name="urgency" required class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                    <option value="">Select priority</option>
                                    <option value="low" @selected(old('urgency') === 'low')>Low — learning / non-urgent</option>
                                    <option value="medium" @selected(old('urgency') === 'medium')>Medium — input requested soon</option>
                                    <option value="high" @selected(old('urgency') === 'high')>High — prompt input needed</option>
                                    <option value="critical" @selected(old('urgency') === 'critical')>Critical — time-sensitive discussion</option>
                                </select>
                                @error('urgency')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label for="description" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Case overview</label>
                            <textarea id="description" name="description" rows="4" required minlength="50"
                                      placeholder="Summarize why this case is difficult, unusual or diagnostically uncertain..."
                                      class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('description') }}</textarea>
                            @error('description')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-teal-600">2. De-identified patient profile</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-900 dark:text-white">Use broad clinical categories only</h2>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="patient_age_group" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Age group</label>
                            <select id="patient_age_group" name="patient_age_group" required class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="">Select age group</option>
                                @foreach(['neonate'=>'Neonate (0–28 days)','infant'=>'Infant (1–12 months)','child'=>'Child (1–12 years)','adolescent'=>'Adolescent (13–17 years)','young_adult'=>'Young adult (18–35 years)','adult'=>'Adult (36–64 years)','older_adult'=>'Older adult (65+ years)'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('patient_age_group') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('patient_age_group')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="patient_sex" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Sex relevant to the case</label>
                            <select id="patient_sex" name="patient_sex" required class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                                <option value="">Select</option>
                                <option value="male" @selected(old('patient_sex') === 'male')>Male</option>
                                <option value="female" @selected(old('patient_sex') === 'female')>Female</option>
                                <option value="other" @selected(old('patient_sex') === 'other')>Other / intersex</option>
                                <option value="not_relevant" @selected(old('patient_sex') === 'not_relevant')>Not clinically relevant</option>
                            </select>
                            @error('patient_sex')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="mt-5">
                        <label for="private_reference" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Private local reference <span class="font-normal text-slate-400">(optional)</span></label>
                        <input id="private_reference" name="private_reference" value="{{ old('private_reference') }}" maxlength="100"
                               placeholder="e.g. your hospital's non-public record code"
                               class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                        <p class="mt-1.5 text-xs leading-5 text-slate-500">Encrypted and visible only to you and administrators. Do not enter the patient's name, phone number or national ID.</p>
                        @error('private_reference')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase tracking-wider text-teal-600">3. Clinical evidence</p>
                        <h2 class="mt-1 text-lg font-bold text-slate-900 dark:text-white">Provide enough detail for meaningful input</h2>
                    </div>
                    <div class="space-y-5">
                        @foreach([
                            ['clinical_history', 'Relevant history', 'Include relevant comorbidities, duration and timeline. Exclude identifying dates and locations.', 4],
                            ['symptoms', 'Presenting signs and symptoms', 'Describe important positive and negative findings.', 4],
                            ['investigation_results', 'Investigations and results', 'Summarize labs, imaging and other tests using de-identified values.', 4],
                            ['prior_treatments', 'Management attempted and response', 'List interventions, doses where relevant, and observed response.', 4],
                        ] as [$name, $label, $placeholder, $rows])
                            <div>
                                <label for="{{ $name }}" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $label }} @if(in_array($name, ['clinical_history', 'symptoms']))<span class="text-rose-500">*</span>@endif</label>
                                <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required(in_array($name, ['clinical_history', 'symptoms']))
                                          placeholder="{{ $placeholder }}"
                                          class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old($name) }}</textarea>
                                @error($name)<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                            </div>
                        @endforeach
                        <div>
                            <label for="discussion_question" class="mb-1.5 block text-sm font-semibold text-slate-700 dark:text-slate-200">Specific question for colleagues <span class="text-rose-500">*</span></label>
                            <textarea id="discussion_question" name="discussion_question" rows="3" required
                                      placeholder="What diagnosis, interpretation or management decision would you like colleagues to discuss?"
                                      class="w-full rounded-xl border-slate-300 focus:border-teal-500 focus:ring-teal-500 dark:border-slate-600 dark:bg-slate-900 dark:text-white">{{ old('discussion_question') }}</textarea>
                            @error('discussion_question')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                    <label class="flex items-start gap-3">
                        <input type="checkbox" name="author_anonymous" value="1" @checked(old('author_anonymous')) class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800 dark:text-slate-100">Hide my name from the discussion</span>
                            <span class="mt-0.5 block text-xs leading-5 text-slate-500">Your identity remains available to administrators for accountability and audit.</span>
                        </span>
                    </label>
                    <label class="mt-5 flex items-start gap-3 rounded-xl bg-slate-50 p-4 dark:bg-slate-900">
                        <input type="checkbox" name="privacy_confirmation" value="1" required class="mt-1 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
                        <span class="text-sm font-medium leading-6 text-slate-700 dark:text-slate-200">I confirm that this case contains no directly identifying patient information and is shared for professional clinical discussion.</span>
                    </label>
                    @error('privacy_confirmation')<p class="mt-2 text-xs text-rose-600">{{ $message }}</p>@enderror
                </section>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('clinical-cases.index') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-200">Cancel</a>
                    <button class="rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-teal-600/20 transition hover:bg-teal-700">Publish clinical discussion</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
