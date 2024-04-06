import { Controller } from '@hotwired/stimulus';
import Imposter from 'imposterjs';
import DataTable from 'datatables.net'
import Dexie from 'dexie';
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
        this.db = new Dexie("FriendDatabase");
        let db = this.db;
        db.version(2).stores({
            friends: `id,name,city,state`,
        });

        console.log("hello from " + this.identifier);
        this.messageTarget.innerHTML = 'loading ' + this.countValue + ' ' + Imposter.name();

        this.db.friends.count().then((x) => console.error('actual count is ' + x));

        this.initScroller(this.tableTarget);
        // this.loadData();
        // this.initScroller(this.tableTarget);
        // this.initDataTable(this.tableTarget);
    }
    loadData()
    {
        // DB with single table "friends" with primary key "id" and
        // indexes on properties "name" and "age"

        let amigos = [];
        for (let i = 1; i <= this.countValue; i++) {
            amigos.push({
                id: i,
                name: Imposter.name(),
                city: Imposter.city(),
                state: Imposter.state()
            });
        }
        let db = this.db;
        console.assert(this.db, "Missing .db!");

        db.friends.bulkPut(amigos)
            .then( (results) => {
                console.log('Bulk Put finished: ' + results);
                db.friends.count().then( (x) => console.error('count after bulkput is ' + x));
                db.friends.offset(10).limit(4).toArray().then(
                    (x) => {
                        console.error(x)
                        this.initScroller(this.tableTarget);
                    });
            });
        return amigos;
    }

    initScroller(el)
    {
        console.log("initScroller");
        new DataTable(el, {
            // data: data,
            columns: [
                {title: 'ID', data: 'id'},
                {title: 'Name', data: 'name'},
                {title: 'City', name:'city', data: 'city', defaultContent: '~'},
                {title: 'State', name:'state', data: 'state', defaultContent: '~'}
            ],
            serverSide: true,
            processing: true,
            ajax:  (params, callback, settings) => {
                console.warn('this.process starting...');
                let db = this.db;
                console.assert(db, "Missing db.");
                console.assert(this.db, "Missing this.db.");
                db.friends.offset(params.start).limit(params.length).toArray().then(
                    (result) => result
                ).then(
                    (out) => {
                        callback({
                            draw: params.draw,
                            data: out,
                            recordsTotal: this.countValue,
                            recordsFiltered: this.countValue
                        })
                    }
                );
            },
            // ajax: this.process,
            scrollCollapse: true,
            scroller: true,
            scrollY: 200
        });
    }

    process(params, callback, settings) {
        console.warn('this.process starting...');
        let db = this.db;
        console.assert(db, "Missing db.");
        console.assert(this.db, "Missing this.db.");
        console.log(db);
        db.friends.count().then((x) => console.error('actual count is ' + x));
        db.friends.offset(params.start).limit(params.length).toArray().then(
            (result) => result
        ).then(
            (out) => {
                console.error(out);
                // callback({
                //     draw: params.draw,
                //     data: out,
                //     recordsTotal: this.countValue,
                //     recordsFiltered: this.countValue
                // })
            }
        );
    }

    //     console.log(params);
    //     console.log(`DataTables is requesting ${params.length} records starting at ${params.start}`);
    //     let friends = this.db.friends;
    //     friends.count()
    //         .then( (results) => console.error(results));
    //
    //     let out = friends.offset().limit().toArray().then(
    //         (data) => {
    //         console.log(data);
    //         }
    //     );
    //     console.log(out);
    // }

    // ...
}
