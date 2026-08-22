# 📈 DineFlow AI - Analytics Algorithms & Business Intelligence Formulas

## 1. Menu Engineering Matrix (Boston Consulting Group Model)

The Menu Engineering Matrix classifies dishes based on **Profit Margin** and **Sales Popularity Index**:

```
                 HIGH POPULARITY
                       ▲
        PLOWHORSE      │       STAR
  (High Vol, Low Margin)│(High Vol, High Margin)
                       │
◄──────────────────────┼──────────────────────► HIGH MARGIN
                       │
           DOG         │      PUZZLE
  (Low Vol, Low Margin) │(Low Vol, High Margin)
                       ▼
                 LOW POPULARITY
```

### Mathematical Definitions:

1. **Item Profit Margin ($M_i$)**:
   $$M_i = P_i - C_i$$
   Where $P_i$ is Item Selling Price, and $C_i$ is Item Raw Material Cost.

2. **Average Menu Margin ($\bar{M}$)**:
   $$\bar{M} = \frac{\sum_{i=1}^{n} M_i}{n}$$

3. **Item Popularity Index ($V_i$)**:
   Total units sold during the evaluation window.

4. **Average Sales Volume ($\bar{V}$)**:
   $$\bar{V} = \frac{\sum_{i=1}^{n} V_i}{n}$$

### Classification Rules:
- **⭐ Star**: $M_i \ge \bar{M} \land V_i \ge \bar{V}$ (Promote heavily, maintain recipe consistency).
- **🐴 Plowhorse**: $M_i < \bar{M} \land V_i \ge \bar{V}$ (Increase price by 5-10% or reduce ingredient cost).
- **🧩 Puzzle**: $M_i \ge \bar{M} \land V_i < \bar{V}$ (Promote in AI Upsell pairings and feature on home screen).
- **🐕 Dog**: $M_i < \bar{M} \land V_i < \bar{V}$ (Consider replacing with seasonal trending dishes).

---

## 2. Revenue Forecasting Formula (Simple Moving Average + Trend Momentum)

Let $R_t$ represent historical daily revenue on day $t$.

$$\text{SMA}_7 = \frac{1}{7} \sum_{k=0}^{6} R_{t-k}$$

**Trend Momentum Coefficient ($\alpha$)**:
$$\alpha = \begin{cases}
1.04 & \text{if } R_t > 1.05 \times R_{t-1} \text{ (Upward Momentum)} \\
0.96 & \text{if } R_t < 0.95 \times R_{t-1} \text{ (Downward Momentum)} \\
1.00 & \text{otherwise (Stable Trend)}
\end{cases}$$

**Forecasted Revenue for Day $t+1$**:
$$\hat{R}_{t+1} = \text{SMA}_7 \times \alpha$$

---

## 3. Dynamic Kitchen SLA Turnaround Estimator

Let $T_{\text{est}}$ be the estimated food preparation time (in minutes):

$$T_{\text{est}} = T_{\text{base}} + \max(0, (N_{\text{items}} - 2) \times 2.5) + (Q_{\text{active}} \times 3.0)$$

Where:
- $T_{\text{base}} = 12 \text{ minutes}$ (Standard single-ticket preparation time)
- $N_{\text{items}}$ = Total quantity of dishes in ticket
- $Q_{\text{active}}$ = Number of active tickets currently in KDS kitchen queue
