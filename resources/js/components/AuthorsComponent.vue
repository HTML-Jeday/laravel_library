<template>
    <div class="container">
        <div class="row justify-content-center">
        <input placeholder="search" v-model='search' > 
        <button @click='sortClick()'>sort</button>
        <!-- Modal -->
        <div class="modal fade" id="createAuthor" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Author</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="first-name" class="form-label">First Name</label>
                        <input type="text" v-model="firstName" class="form-control" id="first-name" placeholder="Name">
                    </div>
                    <div class="mb-3">
                        <label for="second-name" class="form-label">Second Name</label>
                        <input type="text" v-model="secondName" class="form-control" id="second-name" placeholder="Secondname">
                    </div>
                    <div class="mb-3">
                        <label for="middle-name" class="form-label">Middle Name</label>
                        <input type="text" v-model="middleName" class="form-control" id="middle-name" placeholder="Middlename">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" @click='createAuthor()' data-bs-dismiss="modal">Save changes</button>
            </div>
            </div>
        </div>
        </div>

      

        <div class="col-md-12">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAuthor">
            add author
            </button>
            <div v-if="!authors">Authors page is empty</div>
            <div v-if="errors" class="error-wrapper">
            <div v-for="(error, i) in errors" :key='i' class="alert alert-danger" role="alert">{{error[0]}}</div>
            </div>
            <div class="authors-wrapper">
                <div class="col-md-4" v-for='(author, i) in filterAuthor' :key='author.id'>
                    <div class="card">
                        <li>{{author.firstName}}</li>
                        <li>{{author.secondName}}</li>
                        <li>{{author.middleName}}</li>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateAuthor" @click='saveData(author)'>update author</button>
                        <button class="btn btn-danger" @click='deleteAuthor(author, i)'>delete author</button>

                        <!-- Modal -->
                        <div class="modal fade" id="updateAuthor" tabindex="-1"  aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Author</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="first-name" class="form-label">First Name</label>
                                        <input type="text"  v-model='cachedAuthor.firstName' class="form-control" id="first-name" placeholder="Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="second-name" class="form-label">Second Name</label>
                                        <input type="text"  v-model='cachedAuthor.secondName' class="form-control" id="second-name" placeholder="Secondname">
                                    </div>
                                    <div class="mb-3">
                                        <label for="middle-name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" v-model='cachedAuthor.middleName' id="middle-name" placeholder="Middlename">
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" @click='updateAuthor()' data-bs-dismiss="modal">Save changes</button>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>


        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li :class="{ disabled: !pagination.prev_page_url }"
                    @click.prevent="getData(pagination.prev_page_url)" 
                    class="page-item">
                    <a class="page-link" href="#">Prev</a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link" href="#">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </a>
                </li>
                <li :class="{ disabled: !pagination.next_page_url }"
                    @click.prevent="getData(pagination.next_page_url)"
                    class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>

        </div>


        </div>
    </div>
</template>

<script>
    export default {
        data: function(){
            return{
                firstName: "",
                secondName: "",
                middleName: "",

                pagination: {},

                cachedAuthor:{
                    firstName: "",
                    secondName: "",
                    middleName: ""
                },
                search: '',


                authors: [],
                errors: []
            }
        },
        computed: {
            filterAuthor() {
                return this.authors.filter(item => {
                    if(item.firstName.toUpperCase().indexOf(this.search.toUpperCase()) !== -1 || item.secondName.toUpperCase().indexOf(this.search.toUpperCase()) !== -1){
                        return item;
                    }
                })
            },
            sortArray() {
                function compare(a, b) {
                    if (a.secondName < b.secondName)
                    return -1;
                    if (a.secondName > b.secondName)
                    return 1;
                    return 0;
                }

                return this.authors.sort(compare);
            }
        },
        methods: {
            getData(url) {
                url = url || '/authors/data'
                axios
                .get(url)
                .then(response => {
                    this.authors = response.data.authors.data;
                    this.makePagination(response.data.authors);
                    this.errors = [];
                })
                .catch(error => this.errors = error.response.data.errors)
            },
            createAuthor() {
                const author = {
                    firstName: this.firstName,
                    secondName: this.secondName,
                    middleName: this.middleName,
                }
               

                 axios
                .post('/authors', author)
                .then(response => {
                    if(this.authors.length == 15){
                        this.authors.push(response.data.author);
                        this.getData();
                    }else{
                        this.authors.push(response.data.author);
                    }
                    
                    this.errors = [];
                })
                .catch(error => this.errors = error.response.data.errors)

                this.firstName = '';
                this.secondName = '';
                this.middleName = '';
            },
            makePagination(response) {
                let pagination = {
                    current_page: response.current_page,
                    last_page: response.last_page,
                    prev_page_url: response.prev_page_url,
                    next_page_url: response.next_page_url
                }
                this.pagination = pagination
            },
            saveData(author) {
                this.cachedAuthor = Object.assign({}, author);

            },
            deleteAuthor(author, i) {
                if(this.authors.length == 15){
                    this.getData();
                }
                this.authors.splice(i, 1);
                axios
               .delete(`/authors/${author.id}`, author)
               .then(response => {
                    this.errors = [];
                })
                .catch(error => this.errors = error.response.data.errors)
            },
            updateAuthor() {  
                const author = {};
                if(this.cachedAuthor.firstName != ''){
                    author.firstName = this.cachedAuthor.firstName;
                }
                 if(this.cachedAuthor.secondName != ''){
                    author.secondName = this.cachedAuthor.secondName;
                }
                 if(this.cachedAuthor.middleName != ''){
                    author.middleName = this.cachedAuthor.middleName;
                }
                axios
                .put(`/authors/${this.cachedAuthor.id}`, author)
                .then(response => {
                    let author = response.data.author;
                      this.authors.forEach(item => {
                        if(item.id == author.id){
                            item.firstName = author.firstName;
                            item.secondName = author.secondName;
                            item.middleName = author.middleName;
                        }
                    })
                    this.errors = [];
                })
                .catch(error => this.errors = error.response.data.errors)
            },
            sortClick () {
                this.sortArray;
            }
            
          
        },
        mounted() {
            this.getData();
        }
    }
</script>
<style>
.authors-wrapper {
  display: flex;
  flex-wrap: wrap;
}
.card li{
    list-style-type: none;
}
.card{
    padding: 10px;
}
.col-md-4{
    margin: 5px;
}
.error-wrapper{
    margin: 10px 0;
}
</style>