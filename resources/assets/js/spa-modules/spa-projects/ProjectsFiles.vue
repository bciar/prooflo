<template>
    <div>
        <form>
            <el-row>
                <el-col :xs="24" :md="8" :offset="8" style="margin-top: 20px">
                    <el-upload ref="upload" drag :data="formData" :headers="headers" action="/api/files/upload" :on-preview="handlePreview" :on-remove="handleRemove"
                        :on-error="handleError" :on-success="handleSuccess" :auto-upload="true" :file-list="fileList" name="photos"
                        multiple list-type="picture">
                        <i class="el-icon-upload"></i>
                        <div class="el-upload__text">Drop file here or
                            <em>click to upload</em>
                        </div>
                        <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>
                    </el-upload>
                </el-col>
            </el-row>
            <el-row>
                <el-col :xs="24" :md="16" :offset="8" style="margin-top: 20px">
                    <!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
                    <el-button type="primary" @click="sendProject()" :loading="sendLoading">Finish & Send</el-button>
                    <el-button @click="cancel()">Cancel</el-button>
                </el-col>
            </el-row>
        </form>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import Ls from '../../services/ls'
    const { email, required } = require('vuelidate/lib/validators')

    export default {
        props: ['project_id', 'revision_id'],
        data() {
            return {
                formData: {
                    file_type: '',
                    owner_type: '',
                    project_id: ''
                },

                fileList: [],
                headers: {},
                saveLoading: false,
                sendLoading: false,
                user_type: 'freelancer',
                savedFiles: []
            }
        },
        mounted() {
            const AUTH_TOKEN = Ls.get('auth.token');
            this.headers = {
                Authorization: `Bearer ${AUTH_TOKEN}`,
                Accept: 'application/json'
            }
        },
        methods: {
            handlePreview() {

            },
            handleRemove() {

            },
            handleError(error) {
                console.log(error)
            },
            handleSuccess(response) {
                console.log(response);
                if (response.status == 1) {
                    this.savedFiles.push(response.data);
                    toastr['success'](response.message, 'Success');
                } else {
                    toastr['error'](response.error, 'Error');
                }
            },
            sendProject() {
                console.log(this.fileList.length);
                var self = this;
                this.$confirm('Are you ready to send this project to client?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    if (this.savedFiles.length > 0) {
                        this.sendLoading = true
                        axios.get('/api/projects/send_project/' + this.project_id + '/' + this.user_type).then(response => {
                            self.$router.push({ path: '/projects' });
                            this.sendLoading = false
                            toastr['success'](response.data.message, 'Success')
                            this.clearForm();

                        }).catch(error => {
                            this.sendLoading = false
                            console.log(error.message)
                        });

                    } else {
                        toastr['error']('You should upload some proofs before send this project to the client', 'Error');
                    }

                }).catch(() => {
                    console.log('canceled')
                });
            },
            finishCreateProject() {
                this.$router.push('/projects');
            },
            jqueryCssAdjust() {
                $('.el-upload__input').css('display', 'none');
            },
            cancel() {
                this.$router.push({ path: '/projects' });
            }
        },
        mounted() {
            this.formData.file_type = 'picture';
            this.formData.owner_type = 'proof';
            this.formData.project_id = this.project_id;
            if (this.revision_id) {
                this.formData.revision_id = this.revision_id;
            }

            this.$nextTick(function () {
                this.jqueryCssAdjust();
            });
        }
    }
</script>

<style scoped>
</style>