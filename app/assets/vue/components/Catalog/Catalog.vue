<template>
    <div class="">
        <div class="row">
            <div class="col" type="button" data-toggle="collapse" :data-target="'#i'+id" aria-expanded="false">
                <div class="row">
                    <div class="col-auto pr-1">
                        <i class="fas fa-folder" aria-hidden="true"></i>
                    </div>
                    <div class="col px-0"> {{ name }} </div>
                    <div class="col-auto pr-0">
                        <router-link :to="'user/'+ author.id">
                            <div class="dataAndLogin"> {{ author.login }} </div>
                        </router-link>
                    </div>
                    <div class="col-auto pr-0">
                        <div class="dataAndLogin"> {{ createdAt }} </div>
                    </div>
                </div>
            </div>
            <div type="button" class="col-auto pr-0" @click="modalUpdate($event)">
                <i class="fas fa-edit" aria-hidden="true"></i>
            </div>
            <div type="button" class="col-auto" @click="deleteCatalog($event)">
                <i class="fas fa-times" aria-hidden="true"></i>
            </div>
        </div>
        <div class="collapse ml-4" :id="'i'+id">
            <div v-if="childsCount > 0">
                <div v-for="child in childs">
                    <catalog :id="child.id"
                             :name="child.name"
                             :author="child.author"
                             :createdAt="child.createdAt"
                             :slug="child.slug"
                             :childsCount="child.childsCount"
                             :documentsCount="child.documentsCount"
                             :parent="child.parent"
                             :childs="child.childs"
                             :documents="child.documents"
                    />
                </div>
            </div>
            <div v-for="document in documents">
                <div class="row">
                    <div class="col-auto pr-2">
                        <i class="fas fa-file" aria-hidden="true"></i>
                    </div>
                    <div class="col px-0">
                        <a :href="document.path"> {{ document.fileName }}
                        </a>
                    </div>
                    <div class="col-auto pr-0">
                        <router-link :to="'user/'+ document.author.id">
                            <div class="dataAndLogin"> {{ document.author.login }} </div>
                        </router-link>
                    </div>
                    <div class="col-auto pr-0">
                        <div class="dataAndLogin"> {{ document.createdAt }} </div>
                    </div>
                    <div type="button" :id="document.id" @click="" class="col-auto pr-0">
                        <i class="fas fa-edit" aria-hidden="true"></i>
                    </div>
                    <div type="button" @click="deleteDocument()" class="col-auto">
                        <i class="fas fa-times" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div type="button" @click="modalCreate($event)">
                <i class="fas fa-plus" aria-hidden="true"></i>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "Catalog",
        props: {
            id: {
                type: String,
                required: true
            },
            name: {
                type: String,
                required: true
            },
            createdAt: {
                type: String,
                required: true
            },
            author: {
                type: Object,
                required: true
            },
            slug: {
                type: String,
                required: true
            },
            childsCount: {
                type: Number,
                required: true
            },
            documentsCount: {
                type: Number,
                required: true
            },
            parent: {
                type: Object,
            },
            childs: {
                type: Array,
                default: () => []
            },
            documents: {
                type: Array,
                default: () => []
            }
        },
        methods: {
            modalCreate(event){
                event.preventDefault();
                console.log(this.id, this.name);
                let element = window.$('#createModal');
                element.attr('catalogid', this.id);
                element.modal('show');
            },
            modalUpdate(event){
                event.preventDefault();
                console.log(this.id, this.name);
                let element = window.$('#updateModal');
                element.attr('catalogid', this.id);
                element.modal('show');
            },
            async deleteCatalog(event){
                event.preventDefault();
                console.log(this.id, this.name);
//                const result = await this.$store.dispatch("catalog/catalogDeleteById", event.id);
            },
            async deleteDocument(event){
                event.preventDefault();
                console.log(this.id, this.name, event.id);
//                const result = await this.$store.dispatch("catalog/documentDeleteById", event.id);
            }
        }
    };
</script>
<style>
    .dataAndLogin {
        font-size: .9rem;
        font-weight: 400;
        color: #747373;
    }
</style>