# PHPCollab SaaS Financial Model
## Pricing Strategy Comparison: Hybrid vs Pure Unlimited

**Date:** 2025-11-08
**Analysis Period:** 3 Years
**Target Market:** Project management for teams 5-500 users

---

## Executive Summary

| Metric | Option A: Hybrid | Option B: Pure Unlimited |
|--------|------------------|--------------------------|
| **Pricing** | $10/user OR $99 unlimited | $99/mo flat (250GB limit) |
| **Break-even customers** | 120 customers | 150 customers |
| **Year 1 net profit** | -$18,000 | -$42,000 |
| **Year 3 revenue** | $648,000 | $891,000 |
| **Year 3 profit margin** | 62% | 54% |
| **Risk level** | Medium | Medium-High |
| **Recommendation** | ⭐ **START HERE** | Scale to this later |

---

## Core Assumptions

### Market & Growth
- **Target market size:** 50,000 potential SMB customers
- **CAC (Customer Acquisition Cost):** $80-120 per customer
- **Churn rate:** 5% monthly Year 1, 3% Year 2, 2% Year 3
- **Growth rate:** Conservative, organic-driven

### Customer Mix (Option A - Hybrid)
| Customer Segment | Avg Users | Monthly Price | % of Customers |
|------------------|-----------|---------------|----------------|
| Small Teams | 5 users | $50 | 40% |
| Medium Teams | 15 users | $150 | 35% |
| Unlimited (10-30 users) | N/A | $99 | 20% |
| Unlimited (30+ users) | N/A | $99 | 5% |

**Average Revenue Per Account (ARPA):** ~$90/month

### Customer Mix (Option B - Pure Unlimited)
| Customer Segment | Avg Users | Monthly Price | % of Customers |
|------------------|-----------|---------------|----------------|
| All customers | Unlimited | $99 | 100% |

**Average Revenue Per Account (ARPA):** $99/month

### Infrastructure Costs (AWS)

**Scaling assumptions:**
- 0-200 customers: Tier 1 infrastructure
- 201-500 customers: Tier 2 infrastructure
- 501-1000 customers: Tier 3 infrastructure

**Storage assumptions:**
- Option A: 300GB average per customer (more features = more usage)
- Option B: 200GB average per customer (enforced 250GB limit)

---

## OPTION A: HYBRID MODEL ($10/user OR $99 unlimited)

### Year 1 Projections

#### Q1 (Months 1-3)
```
Customer Growth:
- Month 1: 15 customers (10 per-user, 5 unlimited)
- Month 2: 25 customers (16 per-user, 9 unlimited)
- Month 3: 40 customers (25 per-user, 15 unlimited)
- Q1 End: 40 total customers

Revenue Breakdown:
Month 1: (10 × $75 avg) + (5 × $99) = $1,245
Month 2: (16 × $75) + (9 × $99) = $2,091
Month 3: (25 × $75) + (15 × $99) = $3,360
Q1 Total: $6,696

Monthly Costs:
Infrastructure: $600/mo (AWS optimized)
Support: $2,000/mo (1 part-time)
Development: $8,000/mo (1 full-time)
Marketing: $1,500/mo (content + ads)
Overhead: $400/mo
Total: $12,500/mo

Q1 Net: $6,696 - $37,500 = -$30,804
```

#### Q2 (Months 4-6)
```
Customer Growth:
- Month 4: 60 customers (churn: -2)
- Month 5: 85 customers (churn: -3)
- Month 6: 110 customers (churn: -4)
- Q2 End: 110 total customers

Revenue:
Month 4: 60 × $90 avg = $5,400
Month 5: 85 × $90 = $7,650
Month 6: 110 × $90 = $9,900
Q2 Total: $22,950

Monthly Costs: $12,500 (same)
Q2 Net: $22,950 - $37,500 = -$14,550
```

#### Q3 (Months 7-9)
```
Customer Growth:
- Month 7: 140 customers (churn: -5)
- Month 8: 165 customers (churn: -6)
- Month 9: 185 customers (churn: -7)
- Q3 End: 185 customers

Revenue:
Month 7: 140 × $90 = $12,600
Month 8: 165 × $90 = $14,850
Month 9: 185 × $90 = $16,650
Q3 Total: $44,100

Monthly Costs:
Infrastructure: $1,200/mo (scaled up)
Support: $3,000/mo
Development: $8,000/mo
Marketing: $2,000/mo
Overhead: $500/mo
Total: $14,700/mo

Q3 Net: $44,100 - $44,100 = $0 (BREAK-EVEN!)
```

#### Q4 (Months 10-12)
```
Customer Growth:
- Month 10: 210 customers (churn: -8)
- Month 11: 235 customers (churn: -9)
- Month 12: 250 customers (churn: -10)
- Q4 End: 250 customers

Revenue:
Month 10: 210 × $90 = $18,900
Month 11: 235 × $90 = $21,150
Month 12: 250 × $90 = $22,500
Q4 Total: $62,550

Monthly Costs: $14,700/mo
Q4 Net: $62,550 - $44,100 = +$18,450
```

#### Year 1 Summary
```
Total Revenue: $136,296
Total Costs: $154,800
Net Profit/Loss: -$18,504
Ending Customers: 250
Monthly Recurring Revenue (MRR): $22,500
Annual Run Rate: $270,000
Customer Lifetime Value (LTV): ~$1,800
CAC Payback Period: 4.5 months
```

### Year 2 Projections

```
Starting Customers: 250
Ending Customers: 600 (churn reduced to 3%)

Quarterly Growth:
Q1: 250 → 350 customers
Q2: 350 → 450 customers
Q3: 450 → 550 customers
Q4: 550 → 600 customers

Average Revenue Per Customer: $92 (slight increase from upsells)

Total Revenue: $491,400
Infrastructure: $28,800 (scaled infrastructure)
Support: $48,000 (1 full-time)
Development: $120,000 (1.5 developers)
Marketing: $36,000
Overhead: $12,000
Total Costs: $244,800

Net Profit: $246,600 (50% margin)
Monthly MRR (end): $55,200
Annual Run Rate: $662,400
```

### Year 3 Projections

```
Starting Customers: 600
Ending Customers: 900 (churn reduced to 2%)

Quarterly Growth:
Q1: 600 → 700 customers
Q2: 700 → 800 customers
Q3: 800 → 850 customers
Q4: 850 → 900 customers

Average Revenue Per Customer: $95 (continued upsells)

Total Revenue: $855,000
Infrastructure: $42,000 (optimized at scale)
Support: $72,000 (1.5 support staff)
Development: $156,000 (2 developers)
Marketing: $54,000
Overhead: $18,000
Total Costs: $342,000

Net Profit: $513,000 (60% margin)
Monthly MRR (end): $85,500
Annual Run Rate: $1,026,000
```

---

## OPTION B: PURE UNLIMITED ($99/month flat)

### Year 1 Projections

#### Q1 (Months 1-3)
```
Customer Growth:
- Month 1: 12 customers (harder sell at $99 for small teams)
- Month 2: 20 customers
- Month 3: 32 customers
- Q1 End: 32 customers

Revenue:
Month 1: 12 × $99 = $1,188
Month 2: 20 × $99 = $1,980
Month 3: 32 × $99 = $3,168
Q1 Total: $6,336

Monthly Costs:
Infrastructure: $500/mo (lower storage usage due to limits)
Support: $2,000/mo
Development: $8,000/mo
Marketing: $2,000/mo (need more marketing to offset harder sell)
Overhead: $400/mo
Total: $12,900/mo

Q1 Net: $6,336 - $38,700 = -$32,364
```

#### Q2 (Months 4-6)
```
Customer Growth:
- Month 4: 50 customers (churn: -2)
- Month 5: 72 customers (churn: -3)
- Month 6: 95 customers (churn: -4)
- Q2 End: 95 customers

Revenue:
Month 4: 50 × $99 = $4,950
Month 5: 72 × $99 = $7,128
Month 6: 95 × $99 = $9,405
Q2 Total: $21,483

Monthly Costs: $12,900
Q2 Net: $21,483 - $38,700 = -$17,217
```

#### Q3 (Months 7-9)
```
Customer Growth:
- Month 7: 125 customers (churn: -5)
- Month 8: 152 customers (churn: -6)
- Month 9: 175 customers (churn: -7)
- Q3 End: 175 customers

Revenue:
Month 7: 125 × $99 = $12,375
Month 8: 152 × $99 = $15,048
Month 9: 175 × $99 = $17,325
Q3 Total: $44,748

Monthly Costs:
Infrastructure: $1,000/mo (scaled)
Support: $3,000/mo
Development: $8,000/mo
Marketing: $2,500/mo
Overhead: $500/mo
Total: $15,000/mo

Q3 Net: $44,748 - $45,000 = -$252 (near break-even)
```

#### Q4 (Months 10-12)
```
Customer Growth:
- Month 10: 205 customers (churn: -8)
- Month 11: 235 customers (churn: -9)
- Month 12: 260 customers (churn: -10)
- Q4 End: 260 customers

Revenue:
Month 10: 205 × $99 = $20,295
Month 11: 235 × $99 = $23,265
Month 12: 260 × $99 = $25,740
Q4 Total: $69,300

Monthly Costs: $15,000
Q4 Net: $69,300 - $45,000 = +$24,300
```

#### Year 1 Summary
```
Total Revenue: $141,867
Total Costs: $183,300
Net Profit/Loss: -$41,433
Ending Customers: 260
Monthly Recurring Revenue (MRR): $25,740
Annual Run Rate: $308,880
Customer Lifetime Value (LTV): ~$1,980
CAC Payback Period: 4.2 months
```

### Year 2 Projections

```
Starting Customers: 260
Ending Customers: 650 (lower churn, attracts larger teams)

Quarterly Growth:
Q1: 260 → 380 customers
Q2: 380 → 500 customers
Q3: 500 → 600 customers
Q4: 600 → 650 customers

Average Revenue Per Customer: $99 (flat pricing)
Storage Overages: ~5% of customers pay $20/mo extra = $99 + $1 avg

Total Revenue: $546,000
Infrastructure: $30,000 (lower due to storage limits)
Support: $42,000 (more self-service docs needed)
Development: $120,000
Marketing: $48,000 (need more volume)
Overhead: $12,000
Total Costs: $252,000

Net Profit: $294,000 (54% margin)
Monthly MRR (end): $64,350
Annual Run Rate: $772,200
```

### Year 3 Projections

```
Starting Customers: 650
Ending Customers: 1,000 (easier to scale with simple pricing)

Quarterly Growth:
Q1: 650 → 775 customers
Q2: 775 → 875 customers
Q3: 875 → 950 customers
Q4: 950 → 1,000 customers

Average Revenue Per Customer: $99
Storage Overages: ~8% pay extra = $99 + $2 avg

Total Revenue: $1,010,100
Infrastructure: $48,000 (still controlled by limits)
Support: $84,000 (2 support staff)
Development: $156,000
Marketing: $72,000
Overhead: $24,000
Total Costs: $384,000

Net Profit: $626,100 (62% margin)
Monthly MRR (end): $101,000
Annual Run Rate: $1,212,000
```

---

## SIDE-BY-SIDE COMPARISON

### Year 1
| Metric | Option A: Hybrid | Option B: Unlimited |
|--------|------------------|---------------------|
| Starting customers | 0 | 0 |
| Ending customers | 250 | 260 |
| Total revenue | $136,296 | $141,867 |
| Total costs | $154,800 | $183,300 |
| **Net profit** | **-$18,504** | **-$41,433** |
| Break-even month | Month 8 | Month 9 |
| MRR (end of year) | $22,500 | $25,740 |
| ARPA | $90 | $99 |
| Customer acquisition | Easier (lower entry) | Harder (higher price) |

**Winner: Option A** - Lower losses, faster break-even

---

### Year 2
| Metric | Option A: Hybrid | Option B: Unlimited |
|--------|------------------|---------------------|
| Starting customers | 250 | 260 |
| Ending customers | 600 | 650 |
| Total revenue | $491,400 | $546,000 |
| Total costs | $244,800 | $252,000 |
| **Net profit** | **$246,600** | **$294,000** |
| Profit margin | 50% | 54% |
| MRR (end of year) | $55,200 | $64,350 |
| Infrastructure costs | $28,800 | $30,000 |

**Winner: Option B** - Higher revenue, better margins at scale

---

### Year 3
| Metric | Option A: Hybrid | Option B: Unlimited |
|--------|------------------|---------------------|
| Starting customers | 600 | 650 |
| Ending customers | 900 | 1,000 |
| Total revenue | $855,000 | $1,010,100 |
| Total costs | $342,000 | $384,000 |
| **Net profit** | **$513,000** | **$626,100** |
| Profit margin | 60% | 62% |
| MRR (end of year) | $85,500 | $101,000 |
| ARR | $1,026,000 | $1,212,000 |

**Winner: Option B** - Scales better long-term

---

## BREAK-EVEN ANALYSIS

### Option A: Hybrid Model

**Fixed Costs per Month:**
- Infrastructure: $600-1,200 (scales with customers)
- Support: $2,000-3,000
- Development: $8,000
- Marketing: $1,500-2,000
- Overhead: $400-500
**Total:** ~$12,500-14,700/month

**Variable Revenue:**
- Average customer value: $90/month

**Break-even calculation:**
```
$12,500 ÷ $90 = 139 customers (conservative)
$14,700 ÷ $90 = 163 customers (with scaled costs)

Target: 120-140 customers for break-even
Time to break-even: 7-8 months
```

---

### Option B: Pure Unlimited

**Fixed Costs per Month:**
- Infrastructure: $500-1,000
- Support: $2,000-3,000
- Development: $8,000
- Marketing: $2,000-2,500
- Overhead: $400-500
**Total:** ~$12,900-15,000/month

**Variable Revenue:**
- Average customer value: $99/month

**Break-even calculation:**
```
$12,900 ÷ $99 = 130 customers (conservative)
$15,000 ÷ $99 = 152 customers (with scaled costs)

Target: 130-155 customers for break-even
Time to break-even: 8-9 months
```

---

## CUSTOMER ACQUISITION ANALYSIS

### Option A: Conversion Funnel

```
Website visitors: 10,000/month
Trial sign-ups: 300 (3% conversion)
Paid conversions: 45 (15% trial-to-paid)

Conversion by tier:
- Small teams ($50/mo): 60% of conversions (easier sell)
- Medium teams ($150/mo): 25% of conversions
- Unlimited ($99/mo): 15% of conversions

Average CAC: $80
- Small team CAC: $60 (easier to acquire)
- Unlimited CAC: $120 (requires more nurturing)
```

### Option B: Conversion Funnel

```
Website visitors: 10,000/month
Trial sign-ups: 250 (2.5% conversion - higher friction)
Paid conversions: 35 (14% trial-to-paid)

Conversion:
- All customers: $99/mo (single offer simplifies)

Average CAC: $100
- Requires more touches to convince small teams
- Attracts larger teams more naturally
```

**Insight:** Option A is easier to sell to small teams, Option B self-selects larger teams.

---

## SENSITIVITY ANALYSIS

### What if growth is slower?

**Scenario: 50% slower customer acquisition**

| Metric | Option A (Year 1) | Option B (Year 1) |
|--------|-------------------|-------------------|
| Ending customers | 125 (vs 250) | 130 (vs 260) |
| Revenue | $68,148 | $70,934 |
| Costs | $154,800 | $183,300 |
| Net profit | **-$86,652** | **-$112,366** |
| Break-even month | Month 15 | Month 17 |

**Impact:** Option A is more resilient to slow growth.

---

### What if storage costs spike?

**Scenario: Customers use 2x expected storage**

| Metric | Option A | Option B |
|--------|----------|----------|
| Year 1 storage costs | $1,800/mo | $1,200/mo |
| Extra annual cost | $14,400 | $9,600 |
| Impact on margin | -10% | -7% |

**Impact:** Option B's storage limits protect from runaway costs.

---

### What if churn is higher?

**Scenario: 8% monthly churn (vs 5%)**

| Metric | Option A (Year 1) | Option B (Year 1) |
|--------|-------------------|-------------------|
| Ending customers | 175 (vs 250) | 182 (vs 260) |
| Revenue | $95,607 | $99,308 |
| Net profit | **-$59,193** | **-$83,992** |

**Impact:** Both suffer, but Option A's lower entry point helps retention.

---

## CASH FLOW ANALYSIS

### Option A: Monthly Cash Flow (Year 1)

```
Month 1:  -$11,255 (Revenue: $1,245, Costs: $12,500)
Month 2:  -$10,409 (Revenue: $2,091, Costs: $12,500)
Month 3:  -$9,140 (Revenue: $3,360, Costs: $12,500)
...
Month 8:  +$450 (Revenue: $14,850, Costs: $14,700) ← BREAK-EVEN
...
Month 12: +$7,800 (Revenue: $22,500, Costs: $14,700)

Cumulative cash needed: $68,000 (maximum drawdown in Month 6)
```

### Option B: Monthly Cash Flow (Year 1)

```
Month 1:  -$11,712 (Revenue: $1,188, Costs: $12,900)
Month 2:  -$10,920 (Revenue: $1,980, Costs: $12,900)
Month 3:  -$9,732 (Revenue: $3,168, Costs: $12,900)
...
Month 9:  +$2,325 (Revenue: $17,325, Costs: $15,000) ← BREAK-EVEN
...
Month 12: +$10,740 (Revenue: $25,740, Costs: $15,000)

Cumulative cash needed: $78,000 (maximum drawdown in Month 7)
```

**Cash requirement:** Option A needs $68K, Option B needs $78K runway.

---

## 3-YEAR CUMULATIVE COMPARISON

| Metric | Option A: Hybrid | Option B: Unlimited |
|--------|------------------|---------------------|
| **Total revenue** | **$1,482,696** | **$1,697,967** |
| Total costs | $741,600 | $819,300 |
| **Total profit** | **$741,096** | **$878,667** |
| Ending customers | 900 | 1,000 |
| Final ARR | $1,026,000 | $1,212,000 |
| Final profit margin | 60% | 62% |
| Total customers acquired | ~1,150 | ~1,280 |
| Average churn | 3.3% | 3.3% |

**Long-term winner: Option B** (+$137K more profit over 3 years)

---

## KEY INSIGHTS

### Option A (Hybrid) Strengths:
✅ **Faster break-even** (Month 8 vs Month 9)
✅ **Lower cash requirement** ($68K vs $78K)
✅ **Easier customer acquisition** (lower entry barrier)
✅ **Better for bootstrapping** (less risky)
✅ **More resilient** to slow growth
✅ **Clearer upgrade path** (per-user → unlimited)

### Option A Weaknesses:
❌ More complex pricing to explain
❌ Lower revenue at scale (Year 3)
❌ Higher support burden (multiple SKUs)
❌ Requires usage tracking

---

### Option B (Pure Unlimited) Strengths:
✅ **Higher revenue at scale** (+$215K in Year 3)
✅ **Simpler messaging** (one price)
✅ **Better margins long-term** (62% vs 60%)
✅ **Storage costs controlled** (enforced limits)
✅ **Attracts larger teams** naturally
✅ **Clear Basecamp competitor** positioning

### Option B Weaknesses:
❌ Harder to acquire small teams
❌ Higher cash requirement ($78K)
❌ Slower break-even (Month 9)
❌ Higher Year 1 losses (-$41K vs -$18K)
❌ More marketing spend needed

---

## RECOMMENDED STRATEGY

### Phase 1: Launch with Option A (Hybrid)
**Months 1-12**

Launch with:
```
Standard: $10/user/month
Unlimited: $99/month
```

**Why:**
- Lower risk (break-even faster)
- Easier to bootstrap ($68K vs $78K needed)
- Captures both small and large customers
- Proven pricing model (follows current Basecamp structure)

**Goals:**
- Reach 250 customers
- Achieve $22K MRR
- Learn customer behavior and usage patterns
- Optimize support and infrastructure

---

### Phase 2: Test Pure Unlimited (Year 2)
**Months 13-24**

**Option 2A - Simplify to Unlimited:**
If data shows:
- ✅ 80%+ of revenue comes from unlimited tier
- ✅ Small teams churning at high rates
- ✅ Support costs high for per-user tier

Then **switch to:**
```
Single plan: $99/month unlimited
```

**Option 2B - Keep Hybrid but Adjust:**
If data shows both tiers performing:
```
Standard: $12/user/month (inflation adjustment)
Unlimited: $99/month (or $109/month)
```

---

### Phase 3: Scale and Optimize (Year 3+)
**Months 25+**

Based on Year 2 learnings:
- Optimize infrastructure costs (consider bare metal)
- Add strategic upsells (priority support, extra storage)
- Consider annual pricing discounts
- Expand to enterprise tier if demand exists

**Potential pricing evolution:**
```
Unlimited: $99/month (or $89/month annual)
Enterprise: $299/month (dedicated support, SSO, custom limits)
```

---

## DECISION MATRIX

Choose **Option A (Hybrid)** if:
- ✅ You're bootstrapping (limited runway)
- ✅ You want to de-risk the launch
- ✅ You want to capture both small and large teams
- ✅ You're okay with pricing complexity
- ✅ You want fastest path to profitability

Choose **Option B (Pure Unlimited)** if:
- ✅ You have $80K+ in funding/runway
- ✅ You want simplest messaging
- ✅ You're targeting mid-large teams primarily
- ✅ You want to directly compete with Basecamp
- ✅ You're willing to lose more in Year 1 for higher Year 3 returns

---

## FINAL RECOMMENDATION

### 🎯 Start with Option A, Evolve to Option B

**Launch Strategy:**
```
Year 1: Hybrid model ($10/user OR $99 unlimited)
        → Validate market, reach break-even faster

Year 2: If data supports, transition to pure unlimited
        → Grandfather existing customers
        → New customers: $99/month unlimited only

Year 3: Scale unlimited model
        → Add enterprise tier at $299/month
        → Consider annual discount ($990/year)
```

**This approach:**
- ✅ Minimizes Year 1 risk ($18K loss vs $41K)
- ✅ Breaks even faster (Month 8 vs Month 9)
- ✅ Gives you data to make informed decisions
- ✅ Allows pivot to unlimited if it proves better
- ✅ Maintains optionality

**Key metrics to watch for pivot decision:**
- Revenue split between tiers (if >70% unlimited → pivot)
- Support cost by tier (if per-user is too expensive → pivot)
- Churn rates by tier
- Customer acquisition cost by tier

---

## ASSUMPTIONS & RISKS

### Key Assumptions:
1. CAC of $80-120 is achievable through content marketing
2. Churn decreases from 5% → 2% over 3 years
3. AWS costs scale linearly with customers
4. Support costs stay under 10% of revenue
5. No major competitive disruption

### Risks:
⚠️ **Storage costs spike** - Mitigate: Enforce limits, charge overages
⚠️ **Customer acquisition slower** - Mitigate: Start with hybrid (easier sell)
⚠️ **Basecamp drops prices** - Mitigate: Differentiate on open-source, features
⚠️ **High support burden** - Mitigate: Invest heavily in docs and self-service
⚠️ **Infrastructure costs higher** - Mitigate: Use spot instances, RIs, optimize early

---

## Next Steps

1. **Validate assumptions** with customer interviews (10-20 potential customers)
2. **Build MVP** with both pricing options as feature flags
3. **Launch closed beta** with 20-30 customers to test pricing
4. **Analyze data** after 3 months to confirm model
5. **Public launch** with chosen pricing strategy
6. **Monitor metrics** monthly and adjust as needed

---

**Questions or need different scenarios modeled? Let me know!**
