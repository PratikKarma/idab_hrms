@extends('layouts.admin')

@section('page-title')
    {{ __('Manage Wages') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item">{{ __('Wages') }}</li>
@endsection

@section('action-button')
    @can('Create Wages')
        <a href="javascript:void(0)" data-url="{{ route('wages.create') }}" data-ajax-popup="true"
            data-title="{{ __('Create New Wages') }}" data-bs-toggle="tooltip" title="" class="btn btn-sm btn-primary"
            data-bs-original-title="{{ __('Create') }}">
            <i class="ti ti-plus"></i>
        </a>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-3">
            @include('layouts.hrm_setup')
        </div>
        <div class="col-9">
            <div class="card">
                <div class="card-body table-border-style">

                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Wages Type') }}</th>
                                    <th>{{ __('Minimun') }}</th>
                                    <th>{{ __('DA') }}</th>
                                    <th width="200px">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($wages as $item)
                                    <tr>
                                        <td>{{ $item->wages_type }}</td>
                                        <td>{{ $item->minimum }}</td>
                                        <td>{{ $item->da }}</td>
                                        <td class="Action">
                                            <span>
                                                @can('Edit Wages')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="javascript:void(0)" class="mx-3 btn btn-sm  align-items-center"
                                                            data-url="{{ URL::to('edit-wages/' . $item->id) }}"
                                                            data-ajax-popup="true" data-size="md" data-bs-toggle="tooltip"
                                                            title="" data-title="{{ __('Edit Wages') }}"
                                                            data-bs-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan


                                                @can('Delete Wages')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open(['method' => 'GET', 'route' => ['wages.delete', $item->id], 'id' => 'delete-form-' . $item->id]) !!}
                                                        <a href="javascript:void(0)"
                                                            class="mx-3 btn btn-sm  align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title=""
                                                            data-bs-original-title="Delete" aria-label="Delete"><i
                                                                class="ti ti-trash text-white text-white"></i></a>
                                                        </form>
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
