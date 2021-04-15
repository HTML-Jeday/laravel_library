<template>
    <div class="container">
        <div class="row justify-content-center">
        <input placeholder="search" v-model='search' > 
        <button @click='sortClick()'>sort</button>
        <!-- Modal -->
        <div class="modal fade" id="createBook" tabindex="-1"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label for="first-name" class="form-label">Name</label>
                        <input type="text" v-model="name" class="form-control" id="name" placeholder="Name">                    </div>
                    <div class="mb-3">
                        <label for="second-name" class="form-label">Description</label>
                        <input type="text" v-model="description" class="form-control" id="description" placeholder="Description">
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" @change="uploadImage" >
                    </div>
                    <date-picker v-model="date" valueType="format"></date-picker>
                    <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect01">Options</label>
                    <select class="form-select" @change='changeSelectedAuthor'>
                        <option 
                        :value="author.id"
                         v-for='author in createBookModal'
                         :key='author.id'>
                         {{author.firstName}} - {{author.secondName}}
                         </option>
    
                    </select>
                    <button @click.prevent='addAuthorToBook()'>add author</button>
                    
                    </div>
                    <div class="authors-wrapper">
                        <div>Choosen authors</div>
                    <li 
                        v-for='author in selectedAuthors' 
                        :key='author.id'>
                        {{author.firstName}} - {{author.secondName}}
                        <button class="btn btn-danger" @click.prevent='removeAddedAuthor(author)'>X</button>
                     </li>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary"  data-bs-dismiss="modal" @click='createBook()'>Save changes</button>
            </div>
            </div>
        </div>
        </div>

      

        <div class="col-md-12">
            <button type="button" @click.prevent='copyAuthors' class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBook">
            add book
            </button>
            <div v-if="books == 0">Books page is empty</div>
            <div v-if="errors" class="error-wrapper">
            <div v-for="(error, i) in errors" :key='i' class="alert alert-danger" role="alert">{{error[0]}}</div>
            </div>
            <div class="authors-container" v-if='books'>
                <div class="col-md-4" v-for='(book, i) in filterBook' :key='book.id'>
                    <div class="card">
                        <li>{{book.name}}</li>
                        <li>{{book.description}}</li>
                        <li><img class='img'  :src="'/storage/images/'+ book.image"></li>
                        <li>{{book.updated_at | formatDate}}</li>

                         <div class="authors-wrapper">
                        <div><strong>Athors:</strong></div>
                        <li 
                            v-for='author in book.authors' 
                            :key='author.id'>
                            {{author.firstName}} - {{author.secondName}}
                        </li>
                        </div>

                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#updateBook" @click='saveData(book)'>update book</button>
                        <button class="btn btn-danger" @click='deleteBook(book, i)'>delete book</button>

                        <!-- Modal -->
                        <div class="modal fade" id="updateBook" tabindex="-1"  aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Update Book</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text"  v-model='cachedBook.name' class="form-control" id="name" placeholder="Name">
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <input type="text"  v-model='cachedBook.description' class="form-control" id="description" placeholder="Description">
                                    </div>
                                     <div class="mb-3">
                                        <input type="file" class="form-control" @change='onchangeUpdateImage' >
                                    </div>
                                    <date-picker v-model="cachedBook.created_at" valueType="format"></date-picker>
                                     <div class="input-group mb-3">
                                    <label class="input-group-text" for="inputGroupSelect01">Options</label>
                                    <select class="form-select" @change='changeSelectedAuthorUpdate'>
                                        <option 
                                        :value="author.id"
                                        v-for='author in updateBookModal'
                                        :key='author.id'>
                                        {{author.firstName}} - {{author.secondName}}
                                        </option>
                    
                                    </select>
                                    <button @click.prevent='addAuthorToBookUpdate' :disabled='updateBookModal.length == 0 '>add author</button>
                                    
                                    </div>
                                    <div class="authors-wrapper">
                                        <div>Authors</div>
                                    <li 
                                        v-for='author in cachedBook.authors' 
                                        :key='author.id'>
                                        {{author.firstName}} - {{author.secondName}}
                                        <button class="btn btn-danger" @click.prevent='removeAddedAuthorUpdate(author)'>X</button>
                                    </li>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" @click='updateBook()' data-bs-dismiss="modal">Save changes</button>
                            </div>
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
                    @click.prevent="getBooks(pagination.prev_page_url)" 
                    class="page-item">
                    <a class="page-link" href="#">Prev</a>
                </li>
                <li class="page-item disabled">
                    <a class="page-link" href="#">
                        Page {{ pagination.current_page }} of {{ pagination.last_page }}
                    </a>
                </li>
                <li :class="{ disabled: !pagination.next_page_url }"
                    @click.prevent="getBooks(pagination.next_page_url)"
                    class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>


        </div>
    </div>
</template>

<script>
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';

    export default {
        components: { DatePicker },
        data: function(){
            return{
                books: null,
                errors: [],
                search: '',
                createBookModal: null,
                updateBookModal: [],
                pagination: {},

                name: '',
                description: '',
                date: null,
                image: '',
                authors: null,
                selectedAuthor: '',
                selectedAuthors: [],

                cachedBook: {}

            }
        },
        computed: {
            filterBook() {
                return this.books.filter((book, i) => {
                    return book.name.toUpperCase().indexOf(this.search.toUpperCase()) !== -1;
                })
            },
            sortArray() {
                function compare(a, b) {
                    if (a.name < b.name)
                    return -1;
                    if (a.name > b.name)
                    return 1;
                    return 0;
                }
                return this.books.sort(compare);
            }
        },
        methods: {
            copyAuthors() {
                this.createBookModal = this.authors;
                this.selectedAuthors = [];
            },
            uploadImage(e) {
                this.image = e.target.files[0];
            },
            addAuthorToBook() {
                if(this.selectedAuthor == '') {
                   this.selectedAuthor = this.createBookModal[0];
                }
                this.selectedAuthors.push(this.selectedAuthor);
                this.createBookModal = this.createBookModal.filter(item => item !== this.selectedAuthor);
                this.selectedAuthor = '';
            },
            addAuthorToBookUpdate(){
                if(this.selectedAuthor == '') {
                   this.selectedAuthor = this.updateBookModal[0];
                }
                this.cachedBook.authors.push(this.selectedAuthor);
                this.updateBookModal = this.updateBookModal.filter(item => item !== this.selectedAuthor);
                this.selectedAuthor = '';
            },
            onchangeUpdateImage(e){
                this.cachedBook.image = e.target.files[0];
            },
            removeAddedAuthor(author) {
                this.createBookModal.push(author);
                this.selectedAuthors = this.selectedAuthors.filter(item => item !== author);
            },
            removeAddedAuthorUpdate(author){
                this.updateBookModal.push(author);
                this.cachedBook.authors = this.cachedBook.authors.filter(item => item !== author );
            },
            changeSelectedAuthor(e){
                const author = this.authors.filter(item => item.id == e.target.value);
                this.selectedAuthor = author[0];
            },
            changeSelectedAuthorUpdate(e){
                const author = this.updateBookModal.filter(item => item.id == e.target.value);
                this.selectedAuthor = author[0];
            },
            createBook() {
                const formData = new FormData();
                if(this.image != ''){
                    formData.append('image', this.image);
                }
                if(this.date != null){
                    formData.append('date', this.date);
                }
                if(this.description != ''){
                    formData.append('description', this.description);
                }
                if(this.name != ''){
                    formData.append('name', this.name);
                }
                
                
                formData.append('authors', JSON.stringify(this.selectedAuthors));
                 
                axios.post('/books', formData, { headers: {
                    'Content-Type': 'multipart/form-data'
                }})
                .then(response => {
                    if(this.books.length == 15){
                        this.books.push(response.data.book);
                        this.getBooks();
                    }else{
                        this.books.push(response.data.book);
                    }
                   
                    this.errors = []; 
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                })
                this.name = '';
                this.description = '';
                this.date = null;
            },
            saveData(book) {
                this.updateBookModal = [...this.authors];
                this.cachedBook.book_id = String(this.cachedBook.book_id);
                this.cachedBook = Object.assign({}, book);
                this.cachedBook.image = '';
                this.cachedBook.authors.forEach((i, idx) =>{
                    this.updateBookModal.forEach((el, indx2) =>{
                        if(i.id == el.id){
                            this.updateBookModal.splice(indx2, 1);
                        }
                    })
                })
            },
            deleteBook(book, i) {
                if(this.books.length == 15){
                    console.log(true);
                    this.getBooks();
                }
                this.books.splice(i, 1);
                axios
               .delete(`/books/${book.id}`, book)
               .then(response => {
                    this.errors = [];
                })
                .catch(error => this.errors = error.response.data.errors)
            },
            updateBook(){
                const formData = new FormData();
                if(this.cachedBook.image != ''){
                    formData.append('image', this.cachedBook.image);
                }
                if(this.cachedBook.created_at != null){
                    formData.append('date', this.cachedBook.created_at);
                }
                if(this.cachedBook.description != ''){
                    formData.append('description', this.cachedBook.description);
                }

                if(this.cachedBook.name != ''){
                    formData.append('name', this.cachedBook.name);
                }
                
                
                formData.append('authors', JSON.stringify(this.cachedBook.authors));
                formData.append("_method", "put");
                axios.post(`/books/${this.cachedBook.id}`, formData, { headers: {
                    'Content-Type': 'multipart/form-data'
                }})
                .then(response => {
                   const book = response.data.book;
                   console.log(book);
                    this.books.forEach(item => {
                        if(item.id == book.id){
                            item.name = book.name;
                            item.created_at = book.created_at;
                            item.image = book.image;
                            item.description = book.description;
                            item.authors = book.authors;
                        }
                    })
                    this.errors = []; 
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                })
            },
            sortClick () {
                this.sortArray;
            },
            getAuthors(){
                axios
                .get('/authors/data')
                .then(response => {
                    this.errors = [];
                    this.authors = response.data.authors.data;
                })
                .catch(error => this.errors = error.response.data.errors);
            },
            getBooks(url){
                url = url || '/books/data'
                axios
                .get(url)
                .then(response => {
                    this.errors = [];
                    this.books = response.data.books.data;
                    this.makePagination(response.data.books);
                })
                .catch(error => this.errors = error.response.data.errors);
            },
             makePagination(response) {
                let pagination = {
                    current_page: response.current_page,
                    last_page: response.last_page,
                    prev_page_url: response.prev_page_url,
                    next_page_url: response.next_page_url
                }
                this.pagination = pagination
            }

        },
        mounted() {
            this.getBooks();
            this.getAuthors();
        }
    }
</script>
<style scoped>
.authors-wrapper{
    padding: 10px;
    flex-direction: column;
}
.authors-container{
    display: flex;
    flex-wrap: wrap;
}

.books-wrapper{
    padding: 10px;
}
.card > li{
    text-align: center;
}
.img{
    max-width: 100%;
    height: 300px;
}
</style>