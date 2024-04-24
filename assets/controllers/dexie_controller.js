import {Controller} from '@hotwired/stimulus';
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
        dbName: String
    }

    recreateDB(db)
    {
        return db.delete().then (()=>db.open());
    }

    connect() {
        super.connect();
        // let dbName = this.dbNameValue;
        this.db = new Dexie(this.dbNameValue);
        this.db.version(2).stores({
            productTable: "++id,price,rating,brand",
            friendTable: "++id,name,state,age,zip"
        })

        console.log(this.dbNameValue + ' database startup.')
        this.db.tables.forEach((x) => console.log(x));
        this.startupFriends(this.db, this.countValue);
        // this.startup(this.db);
        // db.on('ready', async vipDB => {
        //     console.log(this.dbNameValue + ' ready')
        // });
        // db.friendTable.count().then( (count) => {
        //     console.log('this should force the ready.')
        // })
        // this.startup(this.db);
        //
        // let reset = false;
        // if (reset) {
        //     Dexie.delete(dbName).then(() => {
        //         console.log("Database successfully deleted " + dbName);
        //     }).catch((err) => {
        //         console.error("Could not delete database " + dbName);
        //     }).finally(() => {
        //         console.log('starting up the reload');
        //         this.startup();
        //     });
        // } else {
        //     this.startup();
        // }

    }

    startup(db)
    {
        console.log(this.dbNameValue + ' database startup.')
        db.on('ready', vipDB => {
            console.log(this.dbNameValue + ' ready')
            // db.delete().then( () => {});
            vipDB.version(1).stores({
                productTable: "++id,price,rating,brand",
                friendTable: "++id,state,age,zip"
            }).then( (x) => {
                console.log(x);
                this.startupFriends(vipDB, this.countValue);
            })
            // this.startupProducts(db);
        });
        // this.db = db; // make it global
        this.db.friendTable.count().then((x) => console.warn(x));

    }

    reload(e)
    {
        console.log('clearing friendTable then creating ' + e.params.count);
        this.db.friendTable.clear().then( async (db) => {
            console.log('friendTable cleared, now creating ' + e.params.count);
            await createFriends(e.params.count).then( (x) => {
                this.db.friendTable.count().then( (c) =>
                {console.log(c);
                    this.messageTarget.innerText = "Table now has " + c
                });
                this.drawTable(this.db);
            })
        })
        // this.db.delete().then ((db)=> {
        //     console.warn("database has been deleted");
        //     this.startupFriends(db, );
        // });


    }



    startupFriends(db, amountToCreate) {
// Populate from AJAX:
        db.on('ready', (db) => {

            // on('ready') event will fire when database is open but
            // before any other queued operations start executing.
            // By returning a Promise from this event,
            // the framework will wait until promise completes before
            // resuming any queued database operations.
            // Let's start by using the count() method to detect if
            // database has already been populated.
            return db.friendTable.count((count) => {
                if (count > 0) {
                    console.log(`Friends Already populated with ${count} objects`);
                } else {
                    console.log("Friends is empty. Populating from ajax call...");
                    return new Promise((resolve, reject) => {
                        console.log('loading...');
                        createFriends(amountToCreate).then((response) => {
                            db.friendTable.clear().then(
                                (x) => db.friendTable.bulkAdd(response)
                            )
                        });
                        // db.someTable.bulkAdd(products);
                        // return this.loadData(db);
                    }).then((data) => {
                        console.log("Got ajax response. We'll now add the objects.");
                        // By returning the a promise, framework will keep
                        // waiting for this promise to complete before resuming other
                        // db-operations.
                        console.log("Calling bulkAdd() to insert objects...");
                        // return db.productTable.bulkAdd(data);
                    }).then(() => {
                        console.log("Done populating.");

                    });
                }
            });
        });

// Following operation will be queued until we're finished populating data:
//         this.initScroller(this.tableTarget);
        console.log('calling the count query after the database is populated');
        this.drawTable(db);
    }

    drawTable(db)
    {
        db.friendTable.count().then((c) => {
            console.log(c);
            this.initScroller(this.tableTarget, db);
        });
    }

    startupProducts(db) {

// Populate from AJAX:
        db.on('ready', (db) => {
            // on('ready') event will fire when database is open but
            // before any other queued operations start executing.
            // By returning a Promise from this event,
            // the framework will wait until promise completes before
            // resuming any queued database operations.
            // Let's start by using the count() method to detect if
            // database has already been populated.
            return db.someTable.count((count) => {
                if (count > 0) {
                    console.log(`Already populated with ${count} objects`);
                } else {
                    console.log("Database is empty. Populating from ajax call...");
                    // We want framework to continue waiting, so we encapsulate
                    // the ajax call in a Promise that we return here.
                    return new Promise((resolve, reject) => {
                        console.log('loading...');
                        loadData().then((response) => {
                            console.error(response);
                            return db.productTable.bulkAdd(response.products);
                        });
                        // db.someTable.bulkAdd(products);
                        // return this.loadData(db);
                    }).then((data) => {
                        console.log("Got ajax response. We'll now add the objects.");
                        // By returning the a promise, framework will keep
                        // waiting for this promise to complete before resuming other
                        // db-operations.
                        console.log("Calling bulkAdd() to insert objects...");
                        // return db.productTable.bulkAdd(data);
                    }).then(function () {
                        console.log("Done populating.");
                    });
                }
            });
        });

// Following operation will be queued until we're finished populating data:
        this.initScroller(this.tableTarget);
        db.productTable.each(function (obj) {
            // When we come here, data is fully populated and we can log all objects.
            // console.log("Found object: " + JSON.stringify(obj));
        }).then(function () {
            console.log("Finished.");
        })
        // }).catch(function (error) {
        //     // In our each() callback above fails, OR db.open() fails due to any reason,
        //     // including our ajax call failed, this operation will fail and we will get
        //     // the error here!
        //     console.error(error.stack || error);
        //     // Note that we could also have caught it on db.open() but in this sample,
        //     // we show it here.
        // });
    }

    // executePaginatedQuery(t: Table, params: {
    //     start: number,
    //     length: number,
    //     filter?: (x: object) => boolean,
    //     order: {name: string, dir: 'asc' | 'desc'}})

    async executePaginatedQuery(t, params) {
        let collection = t;
        console.warn(`${params.length} starting at  ${params.start} from ${t.name}`);
        console.log(t, params);
        // collection = await collection.where('name').anyOfIgnoreCase('t');
        params.order.forEach((o) => {
            // if (o.dir == 'desc') {
            //     t.reverse();
            // }
            if (o.name !== '') {
                // db.friendTable.sortBy(o.name);
                collection = collection.orderBy(o.name) // Requires that this property is indexed
                if (o.dir === "desc") {
                    collection = collection.reverse()
                }
                console.warn(`ordered by ${o.name}`);
            }
        })


            if ( ('search' in params) && (params.search.value)) {
                collection = collection.filter( (t) => new RegExp(params.search.value, 'i').test(t.name));
            }


        collection = collection
            .offset(params.start)
            .limit(params.length)


        console.log(collection);

        return collection.toArray()
    }


    initScroller(el, db) {
        console.log("initScroller");
        new DataTable(el, {
            // data: data,
            columns: [
                {title: 'ID', name: 'id', data: 'id', searchable: false},
                {title: 'Name', name: 'name', data: 'name', orderable: false},
                {title: 'Age', name: 'age', data: 'age', searchable: false},
                // {title: 'Cost', data: 'price'},
                // {title: 'Brand', data: 'brand'},
                // {title: 'Category', data: 'category'},
                // {title: 'City', name:'city', data: 'city', defaultContent: '~'},
                {title: 'State', name: 'state', data: 'state', defaultContent: '~'},
                {title: 'Zip', name: 'zip', data: 'zip', defaultContent: '~'}
            ],
            serverSide: true,
            processing: true,
            ajax: (params, callback, settings) => {
                console.warn('this.process starting...');
                console.assert(db, "Missing db.");
                console.error(params);
                let q = this.executePaginatedQuery(db.friendTable, params);
                q.then(
                    (out) => {
                        callback({
                            draw: params.draw,
                            data: out,
                            recordsTotal: this.countValue,
                            recordsFiltered: this.countValue
                        })
                    }
                );
                // params.order.forEach((o) => {
                //     console.log(o);
                //     // if (o.dir == 'desc') {
                //     //     t.reverse();
                //     // }
                //     if (o.name !== '') {
                //         // db.friendTable.sortBy(o.name);
                //     }
                // })
                //
                // db.friendTable.offset(params.start).limit(params.length).toArray().then(
                //     (result) => result
                // ).then(
                // );
            },
            // ajax: this.process,
            scrollCollapse: true,
            scrollY: 200,
            scroller: true,
            layout: {
                bottomEnd: null
            },
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

async function loadData() {
    // someday: load pages
    // https://css-tricks.com/why-using-reduce-to-sequentially-resolve-promises-works/
    let url = 'https://dummyjson.com/products';
    const response = await fetch(url);
    return await response.json();
}

async function createFriends(numberToCreate) {
    let amigos = [];
    for (let i = 1; i <= numberToCreate; i++) {
        amigos.push({
            id: i,
            name: Imposter.name(),
            age: Imposter.randomNumber(),
            city: Imposter.city(),
            state: Imposter.state(),
            zip: Imposter.postalCode(),
        });
    }
    console.log('returning amigos: ' + amigos.length)
    return amigos;
}

