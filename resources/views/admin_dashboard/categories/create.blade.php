@extends("admin_dashboard.layouts.app")

@section("wrapper")
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
								<li class="breadcrumb-item active" aria-current="page">Thêm mới danh mục</li>
							</ol>
						</nav>
					</div>
				</div>
				<!--end breadcrumb-->
			  
				<div class="card">
				  <div class="card-body p-4">
					  <h5 class="card-title">Thêm danh mục mới</h5>
					  <hr/>
					<form action="{{ route('admin.categories.store') }}" method="POST">
						@csrf

                       <div class="form-body mt-4">
							<div class="row">
								<div class="col-lg-12">
									<div class="border border-3 p-4 rounded">
										<div class="mb-3">
											<label for="inputProductTitle" class="form-label">Tên danh mục</label>
											<input type="text" value=' {{ old("name" ) }}' name="name" required  class="inputCategoryName form-control" id="inputProductTitle" placeholder="Nhập tiêu đề bài viết">
										
											@error('name')
												<p class="text-danger">{{ $message }}</p>
											@enderror
										</div>

										<div class="mb-3">
											<label for="inputProductTitle" class="form-label">Slug - danh mục</label>
											<input type="text" value=' {{ old("slug" ) }}' name="slug" required  class="slugCategory form-control" id="inputProductTitle" placeholder="Nhập slug">
										
											@error('slug')
												<p class="text-danger">{{ $message }}</p>
											@enderror
										</div>

										<button class="btn btn-primary" type="submit">Thêm mới</button>
										<a class="btn btn-secondary" href="{{ route('admin.categories.index')}}">Danh sách</a>
									</div>
								</div>
							</div>
						</div>

					</form>
				  </div>
			  </div>


			</div>
		</div>
		<!--end page wrapper -->
@endsection
	
@section("script")
	<script>
		$(document).ready(function () {

			setTimeout(()=>{
					$(".general-message").fadeOut();
			},5000);

		});

		//slug
		$(document).on('change', '.inputCategoryName', (e) => {
		e.preventDefault();

		let $this = e.target;

		let csrf_token = $($this).parents("form").find("input[name='_token']").val();
		let nameCategory =  $($this).parents("form").find("input[name='name']").val();
		
		let formData = new FormData();
		formData.append('_token', csrf_token);
		formData.append('name', nameCategory);
		
		$.ajax({
			url: "{{ route('admin.categories.to_slug') }}",
			data: formData,
			type: 'POST',
			dataType: 'JSON',
			processData: false,
			contentType: false,
			success: function (data) {
				if(data.success){
					$('.slugCategory').val(data.message);

				}else{
					alert("Bị lỗi khi nhập name !")
				}
			}
		})
	})
	</script>

@endsection