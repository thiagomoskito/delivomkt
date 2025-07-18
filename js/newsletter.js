// Aguarda o documento estar pronto para executar o script
document.addEventListener('DOMContentLoaded', function () {

  // Seleciona os elementos com os quais vamos trabalhar
  const newsletterForm = document.getElementById('form-newsletter');
  const statusDiv = document.getElementById('newsletter-status');

  // Verifica se o formulário existe na página antes de adicionar o listener
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function (event) {
      // Impede o envio padrão do formulário (que recarrega a página)
      event.preventDefault();

      // Pega o campo do e-mail e o checkbox
      const emailInput = newsletterForm.querySelector('input[name="email"]');
      const aceiteCheckbox = document.getElementById('updatesCheck');

      // Validação: verifica se o checkbox de aceite está marcado
      if (!aceiteCheckbox.checked) {
        statusDiv.innerHTML = `<div class="alert alert-warning">Você precisa aceitar os termos para se inscrever.</div>`;
        return; // Para a execução se não estiver marcado
      }

      // Exibe mensagem de carregamento
      statusDiv.innerHTML = `<div class="alert alert-info">Cadastrando...</div>`;

      // Coleta os dados do formulário para envio
      const formData = new FormData(newsletterForm);

      // Envia os dados para o script PHP via Fetch API
      fetch('assinar_newsletter.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json()) // Espera uma resposta JSON do PHP
        .then(data => {
          // Exibe a mensagem de sucesso ou erro retornada pelo PHP
          if (data.status === 'success') {
            statusDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            newsletterForm.reset(); // Limpa os campos do formulário
          } else {
            statusDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
          }
        })
        .catch(error => {
          // Trata erros de conexão ou do servidor
          console.error('Erro no fetch:', error);
          statusDiv.innerHTML = `<div class="alert alert-danger">Ocorreu um erro inesperado. Tente novamente.</div>`;
        });
    });
  }
});