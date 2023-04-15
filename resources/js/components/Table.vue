<template>
<div>
    <table class="table table-hover">
        <thead>
            <tr>
                <td scope="col" v-for="t, key in titulos" :key="key">{{ t.titulo }}</td>
                <th v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
                    
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="obj, chave in dadosFiltrados" :key="chave">
                <td v-for="valor, chaveValor in obj" :key="chaveValor">
                    <span v-if="titulos[chaveValor].tipo == 'texto'">{{ valor }}</span>
                    <span v-if="titulos[chaveValor].tipo == 'data'">{{ valor | formataDataTempoGlobal }}</span>
                    <span v-if="titulos[chaveValor].tipo == 'imagem'">
                        <img :src="'/storage/'+valor" width="30" height="30">
                    </span>
                </td>
                <td v-if="visualizar.visivel || atualizar.visivel || remover.visivel">
                    <button class="btn btn-outline-primary btn-sm" v-if="visualizar.visivel" :data-toggle="visualizar.dataToggle" :data-target="visualizar.dataTarget" @click="setStore(obj)">Visualizar</button>
                    <button class="btn btn-outline-primary btn-sm" v-if="atualizar.visivel" :data-toggle="atualizar.dataToggle" :data-target="atualizar.dataTarget" @click="setStore(obj)">Atualizar</button>
                    <button class="btn btn-outline-danger btn-sm" v-if="remover.visivel" @click="setStore(obj)" :data-toggle="remover.dataToggle" :data-target="remover.dataTarget">Remover</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
</template>

<script>
    export default {
        props: ['dados', 'titulos', 'atualizar', 'visualizar', 'remover'],
        methods: {
            setStore(obj) {
                this.$store.state.transacao.status = ''
                this.$store.state.transacao.mensagem = ''
                this.$store.state.transacao.dados = ''
                this.$store.state.item = obj
            }
        },
        computed: {
            dadosFiltrados() {

                let campos = Object.keys(this.titulos)
                let dadosFiltrados = []
                

                this.dados.map((item, chave) => {
                    

                    let itemFiltrado = {}
                    campos.forEach(campo => {
                        

                        itemFiltrado[campo] = item[campo] // utilizar a sintaxe de array para atribuir valores a objetos
                        
                        
                    })
                    dadosFiltrados.push(itemFiltrado)
                    
                })

                
                return dadosFiltrados //retorne um array de objetos
            }
        }
    }
</script>
