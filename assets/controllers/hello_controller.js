import { Controller } from '@hotwired/stimulus';

import jQuery from 'jquery';
import DataTables from 'datatables.net-bs5'
import 'datatables.net-select-bs5';
import 'datatables.net-bs5/css/dataTables.bootstrap5.min.css';

export default class extends Controller {
    connect() {
        let dt = new DataTables(this.element);
    }
}
