@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Edit Faq   </h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">
            {{-- Additional buttons or actions can go here --}}
        </div>
    </div>

    <div class="row">
        <div class="card">
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success" role="alert">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('faq.update', $faq->id) }}" method="POST">
                    @csrf
                    @method('PUT') <!-- Use PUT for updating -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id">Faq Category</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $faqCategory)
                                    <option value="{{ $faqCategory->id }}" 
                                        {{ $faq->category_id == $faqCategory->id ? 'selected' : '' }}>
                                        {{ $faqCategory->category_name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                @error('category_id')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="question">Faq Question</label>
                            <input type="text" name="question" id="question" class="form-control" 
                                   placeholder="Faq Question" value="{{ old('question', $faq->question) }}">
                            <span class="text-danger">
                                @error('question')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="answer">Faq Answer</label>
                            <textarea name="answer" id="answer" class="form-control" cols="30" rows="10">{{ old('answer', $faq->answer) }}</textarea>
                            <span class="text-danger">
                                @error('answer')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('additional-script')
<script>
    $(document).ready(function() {
        // $('.nav-link').removeClass('active');
        $('.nav-item').removeClass('active');
        $('.collapse').removeClass('show');
    });
</script>
@endsection
