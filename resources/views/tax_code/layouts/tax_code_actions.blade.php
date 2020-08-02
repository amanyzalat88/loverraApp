<div class="dropdown">
    <button class="btn btn-sm btn-outline-primary dropdown-toggle actions-dropdown-btn" type="button" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-h actions-dropdown"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown">
        @if (check_access(array('A_DETAIL_TAXCODE'), true))
            <a href="{{ $tax_code['detail_link'] }}" class="dropdown-item">View</a>
        @endif
        @if (check_access(array('A_EDIT_TAXCODE'), true))
            <a href="edit_tax_code/{{ $tax_code['slack'] }}" class="dropdown-item">Edit</a>
        @endif
    </div>
</div>