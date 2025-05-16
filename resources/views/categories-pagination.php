<?php
    echo '<nav><ul class="pagination justify-content-center">';
    if ($total_pages > 1) {
        if ($current_page > 1) {
            echo '<li class="page-item"><a href="#" class="page-link pagination-link" data-page="'.($current_page - 1).'">&laquo;</a></li>';
        }
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $current_page) ? 'active' : '';
            echo '<li class="page-item '.$active.'"><a href="#" class="page-link pagination-link" data-page="'.$i.'">'.$i.'</a></li>';
        }
        if ($current_page < $total_pages) {
            echo '<li class="page-item"><a href="#" class="page-link pagination-link" data-page="'.($current_page + 1).'">&raquo;</a></li>';
        }
    }

    echo '</ul></nav>';


?>