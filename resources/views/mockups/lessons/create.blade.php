<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link rel="stylesheet" href="{{ mix('css/main.css') }}">
</head>
<body class="flex h-screen overflow-hidden">
    <aside class="w-64 h-full overflow-y-auto text-gray-100 bg-gray-700">
        <div class="flex items-center h-16 px-4 text-lg uppercase bg-gray-900 shadow"> 
            <span class="font-serif text-3xl font-medium text-white">sig</span><span class="ml-2">- ceproesc</span>
        </div>
        <nav class="px-2 py-4">
            <ul class="text-gray-100">
                <li>
                    <a href="#" class="flex items-center px-2 py-2 group hover:bg-gray-800 rounded-md">
                        <x-icons.register-lesson class="w-6 text-gray-300 group-hover:text-gray-200"/>
                        <span class="ml-4 group-hover:text-white">Registro de Aulas</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <main class="w-full h-full overflow-y-auto bg-gray-200">

        <nav class="w-full h-16 bg-white shadow"></nav>

        <div class="px-10 py-10 space-y-8">

            <div>
                <h2 class="text-xl font-medium text-gray-700 capitalize">detalhes da aula</h2>
                <div class="px-6 py-2 mt-4 capitalize bg-white shadow divide-y rounded-md">
                    <div class="flex items-center py-4">
                        <div class="w-1/4">
                            <span class="text-gray-600">turma</span>
                        </div>
                        <div>
                            <span class="inline-block pr-2 font-medium">2020 - julho</span>
                            <span class="inline-block px-2 font-medium border-l">2021 - janeiro</span>
                        </div>
                    </div>
                    <div class="flex items-center py-4">
                        <div class="w-1/4">
                            <span class="text-gray-600">disciplina</span>
                        </div>
                        <div>
                            <span class="font-medium">administração</span>
                        </div>
                    </div>
                    <div class="flex items-center py-4">
                        <div class="w-1/4">
                            <span class="text-gray-600">especialidade</span>
                        </div>
                        <div>
                            <span class="font-medium">administração básica</span>
                        </div>
                    </div>
                    <div class="flex items-center py-4">
                        <div class="w-1/4">
                            <span class="text-gray-600">carga horária</span>
                        </div>
                        <div>
                            <span class="font-medium">32 hrs</span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-medium text-gray-700 capitalize">lista de presença</h2>

                <div class="mt-4 overflow-hidden capitalize shadow rounded-md">

                    <div class="capitalize bg-gray-100 border-b">
                        <div class="px-6 py-2 font-mono text-sm font-bold tracking-wide text-gray-600 uppercase grid-cols-12 grid">
                            <div class="col-span-1"><span>código</span></div>
                            <div class="col-span-5"><span>nome</span></div>
                            <div class="col-span-1"><span>turma</span></div>
                            <div class="col-span-5"><span>presença</span></div>
                        </div>
                    </div>

                    <div class="capitalize bg-white divide-y">

                        <div class="px-6 py-4 text-base capitalize bg-white grid-cols-12 grid">
                            <div class="col-span-1"><span>123</span></div>
                            <div class="col-span-5"><span>Kaua Lima Pereira</span></div>
                            <div class="col-span-1"><span>2021 - janeiro</span></div>
                            <div class="col-span-5">
                                <div class="space-x-4">
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence1" value="0">
                                        <span>0</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence1" value="1">
                                        <span>1</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence1" value="2">
                                        <span>2</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence1" value="3" checked>
                                        <span>3</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="px-6 py-4 text-base capitalize bg-white grid-cols-12 grid">
                            <div class="col-span-1"><span>456</span></div>
                            <div class="col-span-5"><span>Thiago Oliveira Cardoso</span></div>
                            <div class="col-span-1"><span>2020 - julho</span></div>
                            <div class="col-span-5">
                                <div class="space-x-4">
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence2" value="0">
                                        <span>0</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence2" value="1">
                                        <span>1</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence2" value="2">
                                        <span>2</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence2" value="3" checked>
                                        <span>3</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 text-base capitalize bg-white grid-cols-12 grid">
                            <div class="col-span-1"><span>789</span></div>
                            <div class="col-span-5"><span>Brenda Araujo Rodrigues</span></div>
                            <div class="col-span-1"><span>2021 - janeiro</span></div>
                            <div class="col-span-5">
                                <div class="space-x-4">
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence3" value="0">
                                        <span>0</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence3" value="1">
                                        <span>1</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence3" value="2">
                                        <span>2</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence3" value="3" checked>
                                        <span>3</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 text-base capitalize bg-white grid-cols-12 grid">
                            <div class="col-span-1"><span>765</span></div>
                            <div class="col-span-5"><span>Roberto Olivera Rodrigues</span></div>
                            <div class="col-span-1"><span>2020 - julho</span></div>
                            <div class="col-span-5">
                                <div class="space-x-4">
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence4" value="0">
                                        <span>0</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence4" value="1">
                                        <span>1</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence4" value="2">
                                        <span>2</span>
                                    </label>
                                    <label class="inline-flex items-center space-x-2">
                                        <input class="form-radio" type="radio" name="presence4" value="3" checked>
                                        <span>3</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-xl font-medium text-gray-700 capitalize">registro da aula</h2>
                <div class="mt-4 overflow-hidden capitalize bg-white shadow rounded-md">
                    <div class="px-6 py-2 divide-y">
                        <div class="flex items-center py-4">
                            <label for="content" class="w-1/4">
                                <span class="text-gray-600">conteúdo ministrado</span>
                            </label>
                            <div class="w-2/4">
                                <textarea class="block w-full form-textarea" id="content" name="content" rows="4" placeholder="Digite aqui o conteúdo ministrado nesta aula"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end px-6 py-4 bg-gray-100 space-x-2">
                        <button class="px-4 py-2 text-sm font-medium leading-none text-teal-100 capitalize bg-teal-500 hover:bg-teal-600 hover:text-white rounded-md shadown">salvar</button>
                        <button class="px-4 py-2 text-sm font-medium leading-none text-teal-100 capitalize bg-teal-500 hover:bg-teal-600 hover:text-white rounded-md shadown">registrar</button>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>
</html>
