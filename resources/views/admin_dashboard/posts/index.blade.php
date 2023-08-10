@extends('admin_dashboard.layouts.app')

@section('wrapper')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Bài viết</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tất cả bài viết</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="container p-3 row">
                        <form action="{{ route('admin.posts.index') }}" class="col-md-10">
                            <div class="form-group row">
                                <div class="col-md-7">
                                    <input type="text" class="form-control" value="{{ $search }}" name="search"
                                        placeholder="Tìm kiếm bài viết (Tiêu đề/Danh mục/Tên người tạo)" value="">
                                </div>
                                <div class="col-md-3">
                                    <select name='status' class="form-select">
                                        @foreach ($listStatus as $key => $value)
                                            <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                </div>
                            </div>
                        </form>
                        <div class="row mt-3 justify-content-between">
                            <div class="col-md-2">
                                <button type="button" class="btn btn-danger" id="deleteAllSelectedRecord">Xoá chọn</button>
                            </div>
                            <div class="col-md-2"><a href="{{ route('admin.posts.create') }}"
                                    class="btn btn-success  radius-30 mt-2 mt-lg-0"><i class="bx bxs-plus-square"></i>Thêm
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
                                    <th>Mã bài viết</th>
                                    <th>Tiêu đề bài viết</th>
                                    <th>Danh mục</th>
                                    <th>Người tạo</th>
                                    <th>Ngày tạo</th>
                                    <th>Trạng thái</th>
                                    <th>Lượt xem</th>
                                    <th>Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($posts as $post)
                                    <tr id="post_ids{{ $post->id }}">
                                        <td>
                                            <input class="checkbox_ids form-check-input me-3" type="checkbox" name="ids" id=""
                                                value="{{ $post->id }}" aria-label="...">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-2">
                                                    <h6 class="mb-0 font-14">ID-{{ $post->post_id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $post->title }}</td>
                                        <td>{{ $post->category->name }}</td>
                                        <td>{{ $post->UserName }}</td>
                                        <td>{{ $post->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div
                                                class="badge rounded-pill @if ($post->approved === 1) {{ 'text-success bg-light-success' }} @else {{ 'text-danger bg-light-danger' }} @endif p-2 text-uppercase px-3">
                                                <i
                                                    class='bx bxs-circle me-1'></i>{{ $post->approved === 1 ? 'Đã phê duyệt' : 'Chưa phê duyệt' }}
                                            </div>
                                        </td>
                                        <td>{{ $post->views }}</td>

                                        <td>
                                            <div class="d-flex order-actions">
                                                <a href="{{ route('admin.posts.edit', $post) }}" class=""><i
                                                        class='bx bxs-edit'></i></a>
                                                <a href="#" data-url="{{ route('admin.posts.destroy', $post) }}"
                                                    data-id="{{ $post->post_id }}" class="ms-3 btn-del"><i
                                                        class='bx bxs-trash text-danger'></i></a>

                                                <form method="post" action="{{ route('admin.posts.destroy', $post) }}"
                                                    id="delete_form_{{ $post->id }}">
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
                        @if (!empty($post))
                            <div style="text-align: right; margin-right: 3%;">Tổng số {{ $post->count() }} bài viết </div>
                        @endif
                        {{ $posts->links() }}
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
                        url: "{{ route('admin.posts.deleteAll') }}",
                        type: "DELETE",
                        data: {
                            ids: all_ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $.each(all_ids, function(key, val) {
                                $('#post_ids' + val).remove();
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
