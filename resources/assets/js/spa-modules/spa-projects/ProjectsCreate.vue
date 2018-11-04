<template>
    <div>
        <el-row>
            <el-col>
                <el-form :model="formData" status-icon :rules="rules" ref="ProjectForm">
                    <el-form-item label="Company name" prop="company">
                        <el-input type="text" v-model="formData.company"></el-input>
                    </el-form-item>
                    <el-form-item label="Project Name" prop="name">
                        <el-input v-model="formData.name"></el-input>
                    </el-form-item>
                    <el-form-item label="Client name" prop="client_name">
                        <el-input type="text" v-model="formData.client_name"></el-input>
                    </el-form-item>
                    <el-form-item label="Client Email" prop="email">
                        <el-input v-model="formData.email"></el-input>
                    </el-form-item>
                    <el-form-item style="text-align: center">
                        <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue</el-button>
                        <el-button @click="cancel('ProjectForm')">Cancel</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
        <router-link to="/" class="close-button">
            <i class="el-icon-close"></i>
        </router-link>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import Ls from '../../services/ls'
    const { email, required } = require('vuelidate/lib/validators')

    export default {
        mixins: [validationMixin],
        data() {
            return {
                formData: {
                    name: '',
                    company: '',
                    client_name: '',
                    email: ''
                },
                rules: {
                    name: [
                        { required: true, message: 'Project name is required', trigger: 'blur' },
                    ],
                    company: [
                        { required: true, message: 'Company name is required', trigger: 'blur' }
                    ],
                    client_name: [
                        { required: true, message: 'Client name is required', trigger: 'blur' },
                    ],
                    email: [
                        { required: true, message: 'Client email is required', trigger: 'blur' },
                        { type: 'email', required: true, message: 'Client email is not valid', trigger: 'blur' }
                    ]
                },

                fileList: [],
                headers: {},
                saveLoading: false,
                project_id: '',
                revision_id: ''
            }
        },

        mounted() {

        },
        methods: {
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.saveLoading = true
                        axios.post('/api/projects/create', this.formData).then(response => {
                            if (response.data.status == 1) {
                                this.saveLoading = false;
                                this.project_id = response.data.data.project_id;
                                this.revision_id = response.data.data.revision_id;

                                toastr['success'](response.data.message, 'Success')
                                this.resetForm('ProjectForm');
                                this.$router.push({ name: 'upload_files_with_revision', params: { project_id: this.project_id, revision_id: this.revision_id } })
                            } else {
                                toastr['error'](response.data.errors[0], 'Error')
                                this.saveLoading = false
                            }

                        }).catch(error => {
                            this.saveLoading = false
                        });
                    } else {
                        toastr['error']('Error creating project. Please fix all the listed issues')
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            cancel(formName) {
                this.resetForm(formName);
                this.$router.push({ path: '/projects' });
            }
        }
    }
</script>