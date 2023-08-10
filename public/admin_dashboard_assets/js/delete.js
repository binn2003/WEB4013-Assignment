
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $(document).on("click", ".btn-del", function () {
        const elementDel = $(this);
        const id = elementDel.data("id");
        const route = elementDel.data("url");
        if (!id || !route) return;
        Swal.fire({
            title: "Bạn có chắc muốn xoá chứ?",
            text: "Bạn sẽ không thể hoàn tác điều này!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Có, xoá đi!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "DELETE",
                    url: route,
                    data: {
                        id,
                    },
                    success: function (response) {
                        elementDel.closest("tr").remove();
                        Swal.fire(
                            "Xoá thành công!",
                            "Tập tin đã xoá.",
                            "success"
                        );
                    },
                    error: function (error) {
                        Swal.fire("Đã xoá!", "Lỗi không thể xoá.", "error");
                    },
                });
            }
        });
    });
});