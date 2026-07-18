/**
 * CUSTOMER FEEDBACK SYSTEM
 */

let allFeedback = [];

async function loadFeedback() {
  showLoader();
  const result = await APIService.getFeedback();
  hideLoader();

  if (!result.success) {
    toast(result.error, 'error');
    return;
  }

  allFeedback = result.data || [];
  renderFeedback();
}

function renderFeedback() {
  const container = $('[data-feedback-container]');

  if (!allFeedback || allFeedback.length === 0) {
    container.innerHTML = `
      <div class="empty-state">
        <p>📝 No feedback yet</p>
      </div>
    `;
    return;
  }

  container.innerHTML = allFeedback
    .sort((a, b) => new Date(b.createdAt || b.created_at) - new Date(a.createdAt || a.created_at))
    .map(
      (feedback) => `
    <div class="feedback-card">
      <div class="feedback-header">
        <h4>${feedback.customerName || 'Anonymous'}</h4>
        <div class="stars">${'⭐'.repeat(feedback.rating || 5)}</div>
      </div>
      
      <p class="feedback-comment">${feedback.comment || 'No comment'}</p>
      
      <p class="feedback-date">
        ${new Date(feedback.createdAt || feedback.created_at).toLocaleDateString('en-IN')}
      </p>
    </div>
  `
    )
    .join('');
}

document.addEventListener('DOMContentLoaded', () => {
  Auth.checkAuth();
  loadFeedback();
  setInterval(loadFeedback, 60000); // Refresh every minute
});
