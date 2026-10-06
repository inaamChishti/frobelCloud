@extends('layouts.superAdminApp')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        .swal2-container {
            z-index: 9999 !important;
        }

        .user-details-container {
            background-color: #fff;
            border: 1px solid #214AB0;
            border-radius: 12px;
            padding: 30px;
            max-width: 900px;
            margin: 50px auto;
            color: #4b4f54;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .user-details-title {
            color: #214AB0;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
        }

        .user-info {
            margin-bottom: 20px;
        }

        .user-info label {
            font-weight: 600;
            font-size: 18px;
            color: #495057;
        }

        .user-info-value {
            font-size: 18px;
            font-weight: 500;
            background-color: #f1f3f5;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #214AB0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: inline-block;
            width: calc(100% - 140px);
            height: 50px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .user-info-value:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .page-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            padding: 20px;
        }

        .page-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 140px;
            height: 120px;
            text-align: center;
            padding: 8px;
            border: 1px solid #214AB0;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .page-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .page-item label {
            font-size: 12px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 5px;
            word-wrap: break-word;
            text-align: center;
        }

        .switch {
            display: inline-block;
            position: relative;
            width: 50px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 50px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            border-radius: 50px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: #214AB0;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        @media (max-width: 767px) {
            .page-item {
                width: 100%;
                height: auto;
            }

            .page-item label {
                font-size: 10px;
            }

            .switch {
                width: 40px;
                height: 25px;
            }

            .switch .slider:before {
                height: 20px;
                width: 20px;
            }
        }
    </style>

    <div class="container">
        <div class="user-details-container">
            <h2 class="user-details-title">User Details for {{ $user->username }}</h2>

            @if (session('success'))
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    Swal.fire({
                        title: 'Success!',
                        text: "{{ session('success') }}",
                        icon: 'success',
                        showLoaderOnConfirm: true,
                        allowOutsideClick: false,
                        onBeforeOpen: () => Swal.showLoading()
                    });
                </script>
            @endif

            <div class="user-info">
                <h2 class="user-details-title">Manage Access</h2>
                <form action="{{ url('super-admin/store/permission/access') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">

                    <div class="page-container">
                        @foreach ([
                'dashboard',
                'create_branch',
                'list_branch',
                'users',
                'roles',
                'grant_permission',

            ] as $name)
                            <div class="page-item">
                                <label for="{{ $name }}">{{ ucwords(str_replace('_', ' ', $name)) }}</label>
                                <label class="switch">
                                    <input type="checkbox" name="{{ $name }}"
                                        @if (is_array(@$permission->page_name) && in_array($name, array_keys(@$permission->page_name))) checked @endif>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Save Permission</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
