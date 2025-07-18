// Aguarda o conteúdo da página ser totalmente carregado antes de executar o script.
document.addEventListener('DOMContentLoaded', function () {

  // Seleciona os elementos do formulário que vamos manipular.
  const form = document.getElementById('cadastro-form');
  const statusDiv = document.getElementById('form-status');

  // --- APLICAÇÃO DAS MÁSCARAS ---

  // Máscara para o campo de celular.
  const celularInput = document.getElementById('celular');
  const celularMask = IMask(celularInput, {
    mask: '(00) 00000-0000'
  });

  // Máscara dinâmica para o campo de CPF/CNPJ.
  const cnpjCpfInput = document.getElementById('cnpj-cpf');
  const cnpjCpfMask = IMask(cnpjCpfInput, {
    mask: [{
        mask: '000.000.000-00',
        maxLength: 11
      },
      {
        mask: '00.000.000/0000-00'
      }
    ]
  });

  // --- LÓGICA DE SUBMISSÃO DO FORMULÁRIO (AJAX) ---

  // Adiciona um ouvinte de evento para o envio do formulário.
  form.addEventListener('submit', function (event) {
    // Previne o comportamento padrão do formulário, que é recarregar a página.
    event.preventDefault();

    // Coleta todos os dados do formulário.
    const formData = new FormData(form);

    // Exibe uma mensagem de "enviando...".
    statusDiv.innerHTML = '<div class="alert alert-info">Enviando, por favor aguarde...</div>';

    // Usa a API Fetch para enviar os dados para o script PHP.
    fetch('enviar_formulario.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json()) // Converte a resposta do PHP para JSON.
      .then(data => {
        // Se o PHP retornar 'success', exibe a mensagem de sucesso.
        if (data.status === 'success') {
          statusDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
          form.reset(); // Limpa o formulário.
          celularMask.updateValue(); // Limpa o valor da máscara.
          cnpjCpfMask.updateValue(); // Limpa o valor da máscara.
        } else {
          // Se o PHP retornar 'error', exibe a mensagem de erro.
          statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
        }
      })
      .catch(error => {
        // Se houver um erro de rede ou no servidor, exibe um erro genérico.
        console.error('Erro:', error);
        statusDiv.innerHTML = '<div class="alert alert-danger">Ocorreu um erro ao enviar o formulário. Tente novamente mais tarde.</div>';
      });
  });
});