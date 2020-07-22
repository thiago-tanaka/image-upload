<template>
    <div class="w-100">
        <a target="blank" :href="originalPath ? originalPath : '#'">
            <img v-show="loaded" @error="error" @load="load" :src="source" width="180" />
        </a>
        <img v-show="!loaded" src="img/uploading.gif" width="180" />
        <input type="file" class="d-none" ref="file" v-on:change="handleFileUpload()" />

        <div class="mt-2">
            <button type="button" class="btn btn-primary" @click="$refs.file.click()">
                <i class="fas fa-image mr-2" style="font-size:1.8em ;"></i>
                <span style="line-height: 20px;">change image</span>
            </button>
            <div class="mt-2 mb-2" :style="{'opacity': showMessage ? 100 : 0}" style="height:20px">
                <span v-show="saved">
                    <i class="fas fa-check ml-2 text-success" style="font-size:1.3em"></i> saved
                </span>
                <div v-show="!saved" class="text-danger">{{ errorMessage }}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        initialImage: null,
        userId: null,
        initialOriginalPath:null
    },
    data: function() {
        return {
            file: "",
            formData: new FormData(),
            source: "",
            showMessage: false,
            loaded: false,
            wasUpdated: false,
            invalidImage: false,
            errorMessage: "",
            saved: false,
            originalPath: ''
        };
    },
    mounted() {
        this.source = this.initialImage;
        this.originalPath = this.initialOriginalPath
    },
    methods: {
        error() {
            this.loaded = true;
        },
        load() {
            this.loaded = true;
            if (this.wasUpdated) {
                this.showMessage = true;
                let self = this;
                setTimeout(function() {
                    self.showMessage = false;
                }, 3000);
            }
        },
        handleFileUpload() {
            this.file = this.$refs.file.files[0];
            this.formData.append("file", this.file);
            this.formData.append("userId", this.userId);
            this.submitFile();
        },
        submitFile() {
            console.log(this.formData, 'formdata')
            this.errorMessage = "";
            this.invalidImage = true;
            var self = this;
            this.loaded = false;
            axios
                .post("/api/images", this.formData, {
                    headers: {
                        "Content-Type": "multipart/form-data"
                    }
                })
                .then(function(response) {
                    console.log("SUCCESS!!");
                    self.source = 'storage/'+ response.data.file_path;
                    self.wasUpdated = true;
                    self.saved = true;
                    self.originalPath = 'storage/' + response.data.original_path
                })
                .catch(function(error) {
                    if (error.response) {
                        if (error.response.data.invalidImage){
                            self.invalidImage = true;
                            self.errorMessage = error.response.data.invalidImage;
                            self.loaded = true;
                            self.showMessage = true;
                            self.saved = false;
                            setTimeout(function() {
                                self.errorMessage = "";
                                self.showMessage = false;
                            }, 3000);
                        }
                    }
                    console.log("FAILURE!!");
                });
        }
    }
};
</script>

<style>
/* img { */
/* border-radius: 50%; */
/* } */
</style>