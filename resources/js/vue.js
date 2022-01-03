window.Vue = require('vue');
window._ = require("lodash");


Vue.component('file-input', {
    props: [
        'name',
        'btnClass',
        'maxCount',
        'maxSize',
        'maxFileSize',
        'canChangeName',
        'accept',
    ],
    data: function() {
        return {
            files: [],
        };
    },
    template:
`   <div class="file-input">
        <button class="btn btn-light" @click="addFile(); $event.preventDefault()">
            Загрузить файл <i class="fas fa-paperclip"></i>
        </button>
        <template v-for="(file, index) in files">
            <input
                type="file"
                :name="name + '[]'"
                :accept="accept"
                :ref="'input' + index"
                @change="selectFile(index)"
            />
            <div class="btn-group" v-if="file.file !== null" :key="index">
                <button class="btn btn-light" @click="$event.preventDefault()" :title="size[index]" disabled>
                    <i v-if="file.icon.match(/^fa/)" :class="file.icon"></i>
                    <img v-else :src="file.icon" alt="" />
                </button>
                <input
                    v-if="canChangeName"
                    class="form-control"
                    type="text"
                    :name="name + '-names[]'"
                    v-model="file.name"
                    :placeholder="file.file.name"
                    @change="changeName(index)"
                />
                <input
                    v-else
                    class="form-control"
                    type="text"
                    :value="file.file.name"
                    readonly
                />
                <button class="btn btn-light" @click="removeFile(index); $event.preventDefault()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>`,
    methods: {
        addFile: function () {
            if (!this.files.length || this.files[this.files.length - 1].file !== null) {
                this.files.push({
                    file: null,
                    name: null,
                    icon: null,
                });
            }
            setTimeout(() => {
                this.$refs['input' + (this.files.length - 1)][0].click();
            }, 10);
        },
        removeFile: function (index) {
            this.files.splice(index, 1);
        },
        selectFile: function (index) {
            let files = this.$refs['input' + index][0].files;
            if (files.length) {
                this.files[index].file = files[0];
                this.files[index].name = files[0].name;
                this.files[index].icon = files[0].type.match(/^image\//)
                        ? URL.createObjectURL(files[0])
                        : 'far fa-file';
            } else {
                this.files.splice(index, 1);
            }
        },
        changeName: function (index) {
            let file = this.files[index];
            let extension = file.file.name.match(/^.+(\..+?)$/)[1].toLowerCase();
            let match = file.name.match(/^.+(\..*?)$/);
            if (match && match[1].length <= extension.length) {
                match[1] = match[1].toLowerCase();
                if (match[1] !== extension) {
                    let m = true;
                    for (let i = 0; i < match[1].length; i++) {
                        if (match[1][i] !== extension[i]) {
                            m = false;
                            break;
                        }
                    }
                    if (m) {
                        file.name = file.name.replace(/\..*?$/, extension);
                    } else {
                        file.name += extension;
                    }
                }
            } else if (file.name.length > 0) {
                file.name += extension;
            }
        },
    },
    computed: {
        size: function () {
            return this.files.map((file) => {
                if (file.file !== null) {
                    let size = file.file.size;
                    let char = 'B';
                    while (char !== 'TB' && size > 1024) {
                        size = size / 1024;
                        switch (char) {
                            case 'GB':
                                char = 'TB';
                                break;
                            case 'MB':
                                char = 'GB';
                                break;
                            case 'KB':
                                char = 'MB';
                                break;
                            case 'B':
                                char = 'KB';
                                break;
                        }
                    }
                    return size.toFixed(2) + char;
                } else {
                    return null;
                }
            });
        },
    },
});
