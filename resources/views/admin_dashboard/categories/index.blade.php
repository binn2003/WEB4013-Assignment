@extends('admin_dashboard.layouts.app')

@section('wrapper')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Danh mục bài viết</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tất cả danh mục</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="card">
                <div class="card-body">
                    <div class="container p-3 justify-between">
                        <div class="row">
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger" id="deleteAllSelectedRecord">Xoá chọn</button>
                            </div>
                            <form action="{{ route('admin.categories.index') }}" class="col-md-8">
                                <div class="form-group row">
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" value="{{ $search }}"
                                            name="search" placeholder="Tìm kiếm tên danh mục">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>
                            <div class="col-md-2"><a href="{{ route('admin.categories.create') }}"
                                    class="btn btn-success radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Thêm
                                    mới</a></div>
                        </div>

                    </div>


                    <div class="table-responsive border">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input class="form-check-input me-3" type="checkbox" value=""
                                            id="select_all_ids">
                                    </th>
                                    <th>Mã Danh mục</th>
                                    <th>Tên danh mục</th>
                                    <th>Người tạo</th>
                                    <th>Xem chi tiết</th>
                                    <th>Ngày tạo</th>
                                    <th>Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr id="category_ids{{ $category->id }}">
                                        <td>
                                            <input class=" checkbox_ids form-check-input me-3" name="ids" id=""
                                                type="checkbox" value="{{ $category->id }}" aria-label="...">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0 font-14">ID-{{ $category->id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $category->name }}</td>
                                        <td>{{ $category->user->name }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm"
                                                href="{{ route('admin.categories.show', $category) }}">Chi tiết bài
                                                viết</a>
                                        </td>
                                        <td>{{ $category->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="d-flex order-actions">
                                                <a href="{{ route('admin.categories.edit', $category) }}" class=""><i
                                                        class='bx bxs-edit'></i></a>
                                                <a href="#"
                                                    data-url="{{ route('admin.categories.destroy', $category) }}"
                                                    data-id="{{ $category->id }}" class="ms-3 btn-del"><i
                                                        class='bx bxs-trash text-danger'></i>
                                                </a>

                                                <form method="post"
                                                    action="{{ route('admin.categories.destroy', $category) }}"
                                                    id="delete_form_{{ $category->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        @if (!empty($category))
                            <div style="text-align: right; margin-right: 3%">Tổng số {{ $category->count() }} danh mục
                            </div>
                        @endif
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>


    </div>
    </div>
    <!--end page wrapper -->
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            setTimeout(() => {
                $(".general-message").fadeOut();
            }, 5000);

        });

        $(function(e) {
            //selectAll
            $("#select_all_ids").click(function() {
                $('.checkbox_ids').prop('checked', $(this).prop('checked'));
            })

            //deleteSelectAll
            $("#deleteAllSelectedRecord").click(function(e) {
                let message;
                if (confirm("Bạn muốn xoá các mục đã chọn?") == true) {
                    e.preventDefault();
                    var all_ids = [];
                    $('input:checkbox[name=ids]:checked').each(function() {
                        all_ids.push($(this).val());
                    })
                    $.ajax({
                        url: "{{ route('admin.categories.deleteAll') }}",
                        type: "DELETE",
                        data: {
                            ids: all_ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $.each(all_ids, function(key, val) {
                                $('#category_ids' + val).remove();
                            })
                        }
                    });
                    // message = "Xoá các mục đã chọn thành công";
                    alert('Xoá các mục đã chọn thành công');
                }
            });
        });
    </script>
@endsection
