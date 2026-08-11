/**
 * DineFlow AI - Real-time Order Tracking Poller & SSE Stream Client
 *
 * @author veer prajapati <veerprajapati2009@gmail.com>
 * @version 1.0.0
 */
class OrderTrackerClient {
  constructor(orderId, onStatusChange, pollIntervalMs = 4500) {
    this.orderId = orderId;
    this.onStatusChange = onStatusChange;
    this.pollIntervalMs = pollIntervalMs;
    this.timerId = null;
    this.isPolling = false;
  }

  start() {
    if (this.isPolling) return;
    this.isPolling = true;
    this.checkStatus();
    this.timerId = setInterval(() => {
      if (document.visibilityState !== 'hidden') {
        this.checkStatus();
      }
    }, this.pollIntervalMs);
  }

  stop() {
    this.isPolling = false;
    if (this.timerId) {
      clearInterval(this.timerId);
      this.timerId = null;
    }
  }

  async checkStatus() {
    try {
      const res = await fetch(`/api/orders/${this.orderId}/status`);
      if (!res.ok) return;
      const data = await res.json();
      if (data && data.data && typeof this.onStatusChange === 'function') {
        this.onStatusChange(data.data);
      }
    } catch (e) {
      console.warn('[OrderTrackerClient] Poll transient failure:', e.message);
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = OrderTrackerClient;
}
