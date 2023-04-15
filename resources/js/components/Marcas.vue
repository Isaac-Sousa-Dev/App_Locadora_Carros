<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- INICIO DA CARD DE BUSCA -->
                <card-component titulo="Busca de Marcas">
                    
                    <template v-slot:conteudo>
                        <div class="form-row">
                            <div class="col mb-3">
                                <input-container-component titulo="ID" id="inputId" idHelp="idHelp" texto-ajuda="Opcional. Informe o ID do registro">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-container-component>
                            </div>

                            <div class="col mb-3">
                                <input-container-component titulo="Nome da Marca" id="inputNome" idHelp="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da Marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="button" class="btn btn-primary btn-sm float-right" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- FIM DO CARD DE BUSCA (MARCAS)-->


                <!-- INICIO DO CARD DE LISTAGEM DE MARCAS -->
                <card-component titulo="Relação de Marcas">
                    <template v-slot:conteudo>
                            <table-component 
                                :dados="marcas.data" :visualizar="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaVisualizar'}" 
                                :atualizar="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaAtualizar'}" 
                                :remover="{ visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaRemover'}" 
                                :titulos="{ 
                                    id: {titulo: 'ID', tipo: 'texto'}, 
                                    nome: {titulo: 'Nome', tipo: 'texto'},
                                    imagem: {titulo: 'Imagem', tipo: 'imagem'},
                                    created_at: {titulo: 'Criação', tipo: 'data'}
                                }">
                            </table-component>   
                    </template>

                    <template v-slot:rodape>

                        <div class="row">
                            <div class="col-10">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key" :class="l.active ? 'page-item active' : 'page-item'" @click="paginacao(l)">
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>
                            <div class="coll">
                                <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>

                        
                        
                    </template>

                </card-component>
                <!-- FIM DO CARD DE LISTAGEM DE MARCAS -->

            </div>
        </div>

        <!--Inicio do Modal de Inclusão de Marca-->
        <modal-component titulo="Adicionar Marca" id="modalMarca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro Realizado com sucesso" v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da Marca" id="novoNome" idHelp="novoNomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da Marca" v-model="nomeMarca">
                    </input-container-component>
                    {{ nomeMarca }}
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="novoImagem" idHelp="novoImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                    {{ arquivoImagem }}
                </div>
            </template> 
            
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!--Final do Modal de Inclusão de Marca-->


        <!--Inicio do Modal de Visualização de Marca-->
        <modal-component titulo="Visualizar Marca" id="modalMarcaVisualizar">
            <template v-slot:alertas>

            </template>

            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'/storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-container-component>

                <input-container-component titulo="Data de Criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!--Fim do Modal de Visualização de Marca-->


        <!--Inicio do Modal de Exclusão de Marca-->
        <modal-component titulo="Remover Marca" id="modalMarcaRemover">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"  v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>

            <template v-slot:conteudo v-if="$store.state.transacao.status != 'sucesso'">
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="remover()" v-if="$store.state.transacao.status != 'sucesso'">Remover</button>
            </template>
        </modal-component>
        <!--Fim do Modal de Exclusão de Marca-->


        <!--Inicio do Modal de Atualização de Marca-->
        <modal-component titulo="Atualizar Marca" id="modalMarcaAtualizar">
            <template v-slot:alertas>
                <alert-component tipo="success" titulo="Transação realizada com sucesso" :detalhes="$store.state.transacao" v-if="$store.state.transacao.status == 'sucesso'"></alert-component>
                <alert-component tipo="danger" titulo="Erro na transação" :detalhes="$store.state.transacao"  v-if="$store.state.transacao.status == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da Marca" id="atualizarNome" idHelp="atualizarNomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                        <input type="text" class="form-control" id="atualizarNome" aria-describedby="atualizarNomeHelp" placeholder="Nome da Marca" v-model="$store.state.item.nome">
                    </input-container-component>
                </div>

                <div class="form-group">
                    <input-container-component titulo="Imagem" id="atualizarImagem" idHelp="atualizarImagemHelp" texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="atualizarImagem" aria-describedby="atualizarImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                    
                </div>

            </template> 
            
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="atualizar()">Atualizar</button>
            </template>
        </modal-component>
        <!--Final do Modal de Atualização de Marca-->
        
        
    </div>
    </template>
    
    <script>
        export default {
            computed: {
                    token() {

                        let t = document.cookie.split(';').find(indice => {
                            return indice.includes('token=')
                        })

                        t = t.split('=')[1]
                        t = 'Bearer ' + t

                        return t
                    }
            },
            data() {
                return {
                    urlBase: 'http://localhost:8000/api/v1/marca',
                    urlPaginacao: '',
                    urlFiltro: '',
                    nomeMarca: '',
                    arquivoImagem: [],
                    transacaoStatus: '',
                    transacaoDetalhes: {},
                    marcas: { data: []},
                    busca: { nome: '', id: '' }
                }
            },
            methods: {
                atualizar() {

                    let formData = new FormData();
                    formData.append('_method', 'patch')
                    formData.append('nome', this.$store.state.item.nome)

                    if(this.arquivoImagem[0]){
                        formData.append('imagem', this.arquivoImagem[0])
                    }
                    

                    let url = this.urlBase + '/' + this.$store.state.item.id

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }

                    axios.post(url, formData, config)
                        .then(response => {
                            this.$store.state.transacao.status = 'sucesso'
                            this.$store.state.transacao.mensagem = 'Registro de Marca Atualizado com sucesso'

                            //limpar o campo de seleção de arquivos
                            atualizarImagem.value = ''
                            this.carregarLista()
                        })
                        .catch(errors => {
                            this.$store.state.transacao.status = 'erro'
                            this.$store.state.transacao.mensagem = errors.response.data.message
                            this.$store.state.transacao.dados = errors.response.data.errors
                        })
                },
                remover() {
                    let confirmacao = confirm('Tem certez que deseja remover esse registro?')

                    if(!confirmacao) {
                        return false;
                    }

                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }

                    let formData = new FormData();
                    formData.append('_method', 'delete')

                    let url = this.urlBase + '/' + this.$store.state.item.id
                    

                    this.$store.state.transacao.status = 'sucesso'
                    this.$store.state.transacao.mensagem = 'registro removido com sucesso'

                    console.log(this.$store.state.transacao)

                    
                    axios.post(url, formData, config)
                        .then(response => {
                            this.$store.state.transacao.status = 'sucesso'
                            this.$store.state.transacao.mensagem = response.data.msg
                            this.carregarLista()
                        })
                        .catch(errors => {
                            this.$store.state.transacao.status = 'erro'
                            this.$store.state.transacao.mensagem = errors.response.data.erro
                        })
                },
                pesquisar() {
                    //console.log(this.busca)

                    let filtro = ''

                    for(let chave in this.busca) {
                        //console.log(chave, this.busca[chave])

                        if(this.busca[chave]){
                            if(filtro != '') {
                            filtro += ';'
                            }
                            filtro += chave + ':like:' + this.busca[chave]
                        }
                        
                    }
                    if(filtro != ''){
                        this.urlPaginacao = 'page=1'
                        this.urlFiltro = '&filtro='+filtro
                    } else {
                        this.urlFiltro = ''
                    }
                    this.carregarLista()
                    
                },
                paginacao(l) {
                    if(l.url) {
                        //this.urlBase = l.url //ajustando a url com o parametro de página
                        this.urlPaginacao = l.url.split('?')[1]
                        this.carregarLista() //requisitando novamente os dados para nossa API
                    }
                    
                },
                carregarLista() {

                    let config = {
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': this.token
                        }
                    }

                    let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro

                    axios.get(url, config)
                        .then(response => {
                            this.marcas = response.data
                        })
                        .catch(errors => {
                            console.log(errors)
                        })
                },
                carregarImagem(e) {
                    this.arquivoImagem = e.target.files
                },
                salvar() {
                    console.log(this.nomeMarca, this.arquivoImagem[0])

                    let formData = new FormData();
                    formData.append('nome', this.nomeMarca)
                    formData.append('imagem', this.arquivoImagem[0])

                    let config = {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                            'Accept': 'application/json',
                            'Authorization': this.t
                        }
                    }

                    axios.post(this.urlBase, formData, config)
                        .then(response => {
                            this.transacaoStatus = 'adicionado' 
                            this.transacaoDetalhes = {
                                mensagem: 'ID do registro ' + response.data.id
                            } 
                            this.carregarLista()
                            console.log(response)
                        })
                        .catch(errors => {
                            this.transacaoStatus = 'erro'
                            this.transacaoDetalhes = {
                                mensagem: errors.response.data.message,
                                dados: errors.response.data.errors
                            } 
                            //errors.response.data.message
                        })
                }
            },
            mounted() {
                this.carregarLista();
            }

            
        }
    </script>
    