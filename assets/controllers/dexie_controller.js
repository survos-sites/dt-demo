import { Controller } from '@hotwired/stimulus';
import Imposter from 'imposterjs';
import DataTable from 'datatables.net'
import 'datatables.net-scroller-bs5'
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['message', 'table']
    static values = {
        count: Number,
    }

    connect()
    {
        super.connect();
        console.log("hello from " + this.identifier);
        this.messageTarget.innerHTML = 'loading ' + this.countValue + ' ' + Imposter.name();
        this.initScroller(this.tableTarget);
        // this.initDataTable(this.tableTarget);
    }
    loadData()
    {
        let friends = [];
        for (let i = 0; i < this.countValue; i++) {
            friends.push({
                name: Imposter.name(),
                city: Imposter.city()
            });
        }
        console.log(friends);
        return friends;
    }

    initScroller(el)
    {
        let data = [];
        for (let i = 0; i < 5000; i++) {
            data.push([i, i*2]);
        }

        new DataTable(el, {
            data: data,
            columns: [
                {title: 'Num'},
                {title: 'double'}
            ],
            scrollCollapse: true,
            scroller: true,
            scrollY: 200
        });
    }
    initDataTable(el)
    {
        let dt = DataTable(el, {
            columns: [
                {title: 'Name'}
            ],
            serverSide: true,
            // data: friends,
            ajax:
                (params, callback, settings) => {
                    console.log(`DataTables is requesting ${params.length} records starting at ${params.start}`);
            }
        });


    }

    // ...
}
