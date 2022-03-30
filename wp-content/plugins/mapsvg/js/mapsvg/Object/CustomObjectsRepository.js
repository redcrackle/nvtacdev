import { Repository } from "../Core/Repository.js";
import { CustomObject } from "./CustomObject.js";
import { Schema } from "../Infrastructure/Server/Schema.js";
export class CustomObjectsRepository extends Repository {
    create(object) {
        const defer = jQuery.Deferred();
        defer.promise();
        this.server
            .post("/objects", this.encodeData(object))
            .done((response) => {
            const data = JSON.parse(response);
            const objectData = this.decodeData(data.object);
            const object = new CustomObject(objectData, this.schema);
            defer.resolve(object);
        })
            .fail(() => {
            defer.reject();
        });
        return defer;
    }
    getLoadedObject(id) {
    }
    findById(id, nocache = false) {
        const defer = jQuery.Deferred();
        defer.promise();
        let object;
        if (!nocache) {
            object = this.objects.findById(id.toString());
        }
        if (!nocache && object) {
            defer.resolve(object);
        }
        else {
            this.server
                .get("/objects/" + id)
                .done((response) => {
                const data = JSON.parse(response);
                const objectData = this.encodeData(data.object);
                const object = new CustomObject(objectData, this.schema);
                defer.resolve(object);
            })
                .fail(() => {
                defer.reject();
            });
        }
        return defer;
    }
    find(query) {
        const defer = jQuery.Deferred();
        defer.promise();
        if (typeof query !== "undefined") {
            this.query.update(query);
        }
        this.server
            .get("/objects/", query)
            .done((response) => {
            const data = JSON.parse(response);
            const objects = [];
            if (data.objects && data.objects.length) {
                this.hasMoreRecords = query.perpage && data.objects.length > query.perpage;
                if (this.hasMoreRecords) {
                    data.objects.pop();
                }
            }
            else {
                this.hasMoreRecords = false;
            }
            data.objects.forEach((object) => {
                objects.push(new CustomObject(this.decodeData(object), this.schema));
            });
            if (data.schema) {
                this.schema = new Schema(data.schema);
            }
            defer.resolve(objects);
        })
            .fail(() => {
            defer.reject();
        });
        return defer;
    }
    getLoaded() {
        return this.objects;
    }
    update(object) {
        const defer = jQuery.Deferred();
        defer.promise();
        this.server
            .put("/objects/" + object.id, object)
            .done((response) => {
            const data = JSON.parse(response);
            const objects = [];
            data.objects.forEach((object) => {
                objects.push(new CustomObject(this.decodeData(object), this.schema));
            });
            if (data.schema) {
                this.schema = new Schema(data.schema);
            }
            defer.resolve(objects);
        })
            .fail(() => {
            defer.reject();
        });
        return defer;
    }
    delete(id) {
        const defer = jQuery.Deferred();
        defer.promise();
        this.server
            .delete("/objects/" + id)
            .done((response) => {
            this.objects.delete(id.toString());
        })
            .fail(() => {
            defer.reject();
        });
        return defer;
    }
    onFirstPage() {
        return this.query.page === 1;
    }
    onLastPage() {
        return this.hasMoreRecords === false;
    }
}
//# sourceMappingURL=CustomObjectsRepository.js.map