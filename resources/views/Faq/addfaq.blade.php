@extends('layouts.main')

@section('main')
    <div class="d-flex justify-content-between align-items-center flex-wrap grid-margin">
        <div>
            <h4 class="mb-3 mb-md-0">Faq Category</h4>
        </div>
        <div class="d-flex align-items-center flex-wrap text-nowrap">

            {{-- <button class="btn btn-primary mb-2 mb-md-0" data-bs-toggle="modal" data-bs-target="#bulkCommissionModal">Bulk Add Commission</button> --}}
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="card-body">
                {{-- <h6 class="card-title">Faq category</h6> --}}
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

                    <form action="{{route('faq.store')}}"  method="POST">
                        @csrf
                        <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="">Faq Category</label>
                            <select name="category_id" id="" class="form-control">
                                <option value="">Select Category</option>
                                @foreach ($categories as $faqCategory)
                                    <option value="{{ $faqCategory->id }}">{{ $faqCategory->category_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger">
                                @error('category_id')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Faq Question</label>
                            <input type="text" name="question" id="" class="form-control" placeholder="Faq Question">
                            <span class="text-danger">
                                @error('question')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="">Faq Answer</label>
                            <textarea name="answer" id="" class="form-control" cols="30" rows="10"></textarea>
                            <span class="text-danger">
                                @error('answer')
                                    {{ $message }}
                                @enderror
                            </span>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                    </form>

                
            </div>
        </div>
    </div>
@endsection

@section('additonal-script')
@endsection
