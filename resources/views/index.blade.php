@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <h5>شجرة التصنيفات</h5>
            <ul>
                @foreach ($allCategories->where('parent_id', null) as $cat)
                    <li>
                        {{ $cat->name }}
                        @if ($cat->children->count())
                            <ul>
                                @foreach ($cat->children as $child)
                                    <li>{{ $child->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-9">
            <button class="btn btn-success mb-2" id="addBtn">إضافة تصنيف</button>
            <input type="text" id="searchInput" value="{{ $search }}" placeholder="بحث" class="form-control mb-2" />
            <div class="tableContainer">
                <form id="deleteForm">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>التصنيف الأب</th>
                                <th>تعديل</th>
                                <th>حذف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $index => $cat)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $cat->id }}"></td>
                                    <td>{{ ($current_page - 1) * 5 + $index + 1 }}</td>
                                    <td>{{ $cat->name }}</td>
                                    <td>{{ optional($cat->parent)->name }}</td>
                                    <td>
                                        <button type="button" class="btn btn-warning editBtn" data-id="{{ $cat->id }}"
                                            data-name="{{ $cat->name }}"
                                            data-parent="{{ $cat->parent_id }}">تعديل</button>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger deleteBtn"
                                            data-id="{{ $cat->id }}">حذف</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-danger">حذف المحدد</button>
                </form>

                <div class="mt-3">
                    @php
                        include base_path('resources/views/categories-pagination.php');
                    @endphp
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="categoryModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إدارة التصنيف</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="catId">
                    <input type="text" id="catName" placeholder="اسم التصنيف" class="form-control mb-2">
                    <select id="catParent" class="form-control">
                        <option value="">بدون أب</option>
                        @foreach ($allCategories->where('parent_id', null) as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button id="saveCategory" class="btn btn-success">حفظ</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            let selectedIds = new Set();

            $('#addBtn').click(() => {
                $('#catId').val('');
                $('#catName').val('');
                $('#catParent').val('');
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            });

            $(document).on('click', '.editBtn', function() {
                $('#catId').val($(this).data('id'));
                $('#catName').val($(this).data('name'));
                $('#catParent').val($(this).data('parent'));
                new bootstrap.Modal(document.getElementById('categoryModal')).show();
            });

            $('#saveCategory').click(function() {
                const id = $('#catId').val();
                const url = id ? '/categories/update' : '/categories/store';
                const data = {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    name: $('#catName').val(),
                    parent_id: $('#catParent').val()
                };

                $.post({
                    url: url,
                    data: data,
                    success: function() {
                        location.reload();
                    },
                    error: function(xhr) {
                        const errors = xhr.responseJSON.errors;
                        let message = '';
                        for (let field in errors) {
                            message += errors[field].join('\n') + '\n';
                        }
                        alert(message);
                    }
                });
            });


            $(document).on('click', '.deleteBtn', function() {
                if (confirm('هل أنت متأكد؟')) {
                    $.ajax({
                        url: '/categories/delete',
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: $(this).data('id')
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });

            $(document).on('submit', '#deleteForm', function(e) {
                e.preventDefault();

                if (selectedIds.size === 0) {
                    alert('الرجاء تحديد العناصر أولاً');
                    return;
                }

                if (!confirm('هل أنت متأكد من حذف المحدد؟')) return;

                $.ajax({
                    url: '/categories/delete',
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ids: Array.from(selectedIds)
                    },
                    success: function() {
                        selectedIds.clear();
                        location.reload();
                    }
                });
            });



            $(document).on('change', 'input[name="ids[]"]', function() {
                const id = $(this).val();
                if (this.checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }

                const allChecked = $('input[name="ids[]"]').length === $('input[name="ids[]"]:checked')
                    .length;
                $('#selectAll').prop('checked', allChecked);
            });

            $(document).on('change', '#selectAll', function() {
                const isChecked = this.checked;
                $('input[name="ids[]"]').each(function() {
                    $(this).prop('checked', isChecked).trigger('change');
                });
            });

            $('#searchInput').on('keyup', function() {
                const search = $(this).val();
                fetchData(1, search);
            });

            $(document).on('click', '.pagination-link', function(e) {
                const page = $(this).data('page');
                const search = $('#searchInput').val();
                fetchData(page, search);
            });

            function fetchData(page = 1, search = '') {
                $.get('/categories', {
                    page,
                    search
                }, function(response) {
                    $('.tableContainer').html($(response).find('.tableContainer').html());

                    $('input[name="ids[]"]').each(function() {
                        const id = $(this).val();
                        if (selectedIds.has(id)) {
                            $(this).prop('checked', true);
                        }
                    });

                    const allChecked = $('input[name="ids[]"]').length === $('input[name="ids[]"]:checked')
                        .length;
                    $('#selectAll').prop('checked', allChecked);
                });
            }
        });
    </script>
@endsection
