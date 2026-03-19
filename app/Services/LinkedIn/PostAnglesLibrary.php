<?php

namespace App\Services\LinkedIn;

class PostAnglesLibrary
{
    /**
     * All 60 swipe post angles.
     * Each has: angle (seed), tags (for logging/filtering), format hint.
     */
    public static function swipeAngles(): array
    {
        return [
            // ── PACKAGING ─────────────────────────────────────────────
            ['angle' => "Walk into any kirana store. 90% of the brands on the shelf look exactly the same. That's your opportunity.", 'tags' => ['packaging', 'india', 'hottake']],
            ['angle' => "Red means spicy. Green means natural. Yellow means cheap. Your packaging colour is saying something — whether you intended it or not.", 'tags' => ['packaging', 'branding']],
            ['angle' => "A food brand spent ₹2L on a packaging redesign. Made it back in 6 weeks. Here's what actually moved the needle.", 'tags' => ['packaging', 'roi']],
            ['angle' => "5 packaging mistakes quietly killing food brand sales in India.", 'tags' => ['packaging', 'india', 'mistake']],
            ['angle' => "Here's exactly what happens when a food brand walks into our studio for a packaging project.", 'tags' => ['packaging', 'process']],
            ['angle' => "Bisleri has had the same bottle design since 1969. Here's why that's a masterclass in brand equity — not laziness.", 'tags' => ['packaging', 'branding', 'india']],
            ['angle' => "Nobody buys a product they can't understand in 3 seconds. Your packaging is your 3-second pitch.", 'tags' => ['packaging', 'hottake']],
            ['angle' => "The packet is the pitch. In 2025, packaging decides if startups sink or survive.", 'tags' => ['packaging', 'india']],
            ['angle' => "How a regional oil brand justified a 30% price increase — without changing the recipe.", 'tags' => ['packaging', 'roi', 'india']],

            // ── BRANDING ──────────────────────────────────────────────
            ['angle' => "Logo is not a brand. Brand is not a logo. Here's what actually separates commodity products from category leaders.", 'tags' => ['branding', 'hottake']],
            ['angle' => "The most underrated branding exercise for food brands: walk your own retail channel as a consumer.", 'tags' => ['branding', 'process']],
            ['angle' => "How much is your brand worth if you remove the product entirely? That's your actual brand equity number.", 'tags' => ['branding', 'india']],
            ['angle' => "We turned down a branding project last month. Here's why — and what it taught us about brand-agency fit.", 'tags' => ['branding', 'founder']],
            ['angle' => "The 4 branding mistakes Indian food founders make in Year 1 — and how each one compounds over time.", 'tags' => ['branding', 'india', 'mistake']],
            ['angle' => "Design is not a cost. It's the cheapest salesperson your brand will ever hire.", 'tags' => ['branding', 'roi']],
            ['angle' => "Why a startup with a ₹5L brand budget will outperform a legacy brand with a ₹50L one.", 'tags' => ['branding', 'india', 'hottake']],
            ['angle' => "The worst time to rebrand is when you're desperate. The best time is when you're growing.", 'tags' => ['rebrand', 'hottake']],

            // ── WEBSITE ───────────────────────────────────────────────
            ['angle' => "A food brand was spending ₹80,000/month on Meta ads. Their website converted at 0.4%. We fixed the site first. Conversion went to 2.1%. Same ad spend. 5x the revenue.", 'tags' => ['website', 'roi', 'india']],
            ['angle' => "The homepage of most Indian food brand websites has 6 problems. Here they are.", 'tags' => ['website', 'india', 'mistake']],
            ['angle' => "A consumer discovers your brand on Instagram. Then they visit your website. And leave in 8 seconds. Here's what happened.", 'tags' => ['website', 'branding']],
            ['angle' => "Most food brand websites are brochures pretending to be stores. Here's the difference.", 'tags' => ['website', 'hottake']],
            ['angle' => "Your FMCG brand's website is losing you sales. Here's the 4-page audit I do for every client.", 'tags' => ['website', 'process']],

            // ── SOCIAL MEDIA ──────────────────────────────────────────
            ['angle' => "Your Instagram grid is your brand's first impression for 60% of new customers. Most food brands treat it like an afterthought.", 'tags' => ['social', 'branding']],
            ['angle' => "The food brands growing fastest on Instagram aren't posting more. They're posting smarter. Here's the 3-content-type system that's working.", 'tags' => ['social', 'india']],
            ['angle' => "Posting every day is not a social media strategy. It's just posting every day.", 'tags' => ['social', 'hottake']],
            ['angle' => "We audited 20 Indian food brand Instagram accounts. Here's what the top 5 had in common — and what the bottom 15 got catastrophically wrong.", 'tags' => ['social', 'india', 'roi']],
            ['angle' => "Most agencies promise 'content that converts'. Here's what actually converts for food and FMCG brands on social.", 'tags' => ['social', 'content']],

            // ── LOGO & IDENTITY ───────────────────────────────────────
            ['angle' => "A founder once told me: 'Just make something clean and simple.' We did. It doubled their inbound inquiries in 90 days.", 'tags' => ['identity', 'roi']],
            ['angle' => "Why we never start a logo project with Illustrator. We start with a blank page and 5 uncomfortable questions.", 'tags' => ['identity', 'process']],
            ['angle' => "Your brand guidelines document is the most valuable file in your company that nobody has ever opened.", 'tags' => ['identity', 'mistake']],
            ['angle' => "Zomato changed their logo in 2021. Dunzo shut down. Swiggy kept iterating. The logo had nothing to do with any of it.", 'tags' => ['identity', 'india', 'hottake']],

            // ── MOTION / VIDEO ────────────────────────────────────────
            ['angle' => "A 60-second animated brand video gets 3x more retention than a static product post. Here's why most brands still won't make one.", 'tags' => ['motion', 'roi']],
            ['angle' => "Your product is 10x better than what your packaging and website communicate. An animated video can close that gap in 90 seconds.", 'tags' => ['motion', 'branding']],
            ['angle' => "The best performing organic content for Indian food brands in 2025 isn't a reel. It's a 45-second explainer with subtitles. Here's why.", 'tags' => ['motion', 'india', 'hottake']],

            // ── PRESENTATIONS ─────────────────────────────────────────
            ['angle' => "A bad pitch deck doesn't just lose the deal. It tells the investor your brand has no story.", 'tags' => ['presentation', 'hottake']],
            ['angle' => "We redesigned a distributor pitch deck for an FMCG brand. They used to close 2 out of 10 meetings. Now it's 6 out of 10. Same founder. Same product. Different story.", 'tags' => ['presentation', 'roi']],
            ['angle' => "5 slides every food brand needs in their distributor pitch deck — and what most founders forget to include.", 'tags' => ['presentation', 'india']],

            // ── CONTENT WRITING ───────────────────────────────────────
            ['angle' => "The copy on most Indian food brand packaging is either a legal disclaimer or a marketing cliché. Neither of them sell.", 'tags' => ['content', 'packaging', 'india']],
            ['angle' => "Nobody reads website copy. They scan it. Here's how to write for the scanner, not the reader.", 'tags' => ['content', 'website']],
            ['angle' => "Your brand story is not 'we started in a small kitchen in 2018'. That's an origin, not a story.", 'tags' => ['content', 'branding', 'hottake']],
            ['angle' => "The 3 types of content that silently destroy brand trust — and most brands post at least one every week.", 'tags' => ['content', 'social', 'mistake']],

            // ── FOUNDER VOICE ─────────────────────────────────────────
            ['angle' => "5 things I wish I had known before starting a design agency in India.", 'tags' => ['founder', 'india']],
            ['angle' => "The most common thing a client says before the best project we've ever done: 'I'm not sure this is the direction.'", 'tags' => ['founder', 'process']],
            ['angle' => "Every brand project we take starts with the same question: What does your customer believe before they see your product?", 'tags' => ['founder', 'process']],
            ['angle' => "The client who gives the most detailed feedback on round 1 is not the most difficult client. They're the most invested one.", 'tags' => ['founder', 'process']],

            // ── INDIA FOCUS ───────────────────────────────────────────
            ['angle' => "Tier 2 and Tier 3 India is the fastest growing FMCG market in the world right now. And most brands are still designing for Delhi and Mumbai.", 'tags' => ['india', 'packaging']],
            ['angle' => "Most Indian food brands are one language. Their biggest growth market speaks three.", 'tags' => ['india', 'branding']],

            // ── ROI ───────────────────────────────────────────────────
            ['angle' => "We tracked 8 FMCG brands before and after a full brand overhaul. 7 out of 8 increased their distributor network within 12 months.", 'tags' => ['roi', 'branding']],
            ['angle' => "How to brief a packaging designer so you don't waste 3 revision cycles explaining what you meant.", 'tags' => ['packaging', 'process']],

            // ── REBRAND ───────────────────────────────────────────────
            ['angle' => "We rebranded a 12-year-old food brand in 90 days. Here's the exact process — and the one thing that almost derailed it.", 'tags' => ['rebrand', 'process']],
            ['angle' => "A water brand rebranded their packaging. Purchase intent went up 3x. They didn't change the product. Just the story on the outside.", 'tags' => ['rebrand', 'roi']],

            // ── EXTRA ─────────────────────────────────────────────────
            ['angle' => "The difference between a brand that grows on shelf and one that gets delisted in 6 months — it's not the product.", 'tags' => ['branding', 'india']],
            ['angle' => "What a good logo brief looks like vs a bad one. The difference is 3 weeks of revisions.", 'tags' => ['identity', 'process']],
            ['angle' => "Why packaging that wins design awards doesn't always win on shelf — and which one you should care about.", 'tags' => ['packaging', 'hottake']],
            ['angle' => "The Instagram bio formula that converts profile visits into DMs for food brands.", 'tags' => ['social', 'content']],
            ['angle' => "Before you spend ₹1 on ads, check these 5 things on your brand.", 'tags' => ['branding', 'roi']],
            ['angle' => "The shelf is a 2-second exam. Most brands don't know what grade they're getting.", 'tags' => ['packaging', 'india', 'hottake']],
            ['angle' => "Colour is the first thing a consumer sees. Typography is the second. Most brands get both wrong.", 'tags' => ['packaging', 'identity']],
            ['angle' => "How we helped a B2B food brand look B2C — and why that was the most important thing we did for them.", 'tags' => ['branding', 'rebrand']],
        ];
    }

    /**
     * All 20 lead magnet angles.
     */
    public static function leadMagnetAngles(): array
    {
        return [
            ['angle' => "I've audited 40+ food brand packaging designs. Most fail on the same 5 points. I built a 20-point checklist so any brand can do this themselves.", 'keyword' => 'AUDIT',    'resource' => 'FMCG Packaging Audit Checklist'],
            ['angle' => "What packaging design choices actually win in kirana stores vs supermarkets? I wrote a guide specifically for general trade.", 'keyword' => 'KIRANA',   'resource' => 'Kirana Shelf Win Guide'],
            ['angle' => "Most food brand stories are timelines. 'We started in a small kitchen in 2019.' That's not a story. I built a template that changes that.", 'keyword' => 'STORY',    'resource' => 'Food Brand Story Template'],
            ['angle' => "How consistent is your brand across packaging, website, social, and print? I built a 15-point scorecard to find out.", 'keyword' => 'SCORE',    'resource' => 'Brand Consistency Scorecard'],
            ['angle' => "I reviewed 30 Indian food brand websites last month. Only 4 of them I'd actually buy from. I documented exactly why — 12 points.", 'keyword' => 'HOMEPAGE', 'resource' => 'Food Brand Homepage Audit'],
            ['angle' => "The exact product page layout I use for FMCG D2C clients — built to convert without relying on discounts.", 'keyword' => 'CONVERT',  'resource' => 'D2C Product Page Conversion Template'],
            ['angle' => "30 days of Instagram content for food brands. Mapped by type, topic, caption direction, and visual brief. No 'post daily with no strategy' nonsense.", 'keyword' => 'CALENDAR', 'resource' => '30-Day FMCG Instagram Content Calendar'],
            ['angle' => "Your Instagram grid is a brand impression in 9 squares. Most food brand grids fail the 3-second scroll test. Here's a 10-minute audit.", 'keyword' => 'GRID',     'resource' => 'Instagram Grid Audit for Food Brands'],
            ['angle' => "Most food brands have a logo. Not a brand identity. There's a big difference — and it costs them every time they brief a designer.", 'keyword' => 'IDENTITY', 'resource' => 'Brand Identity Starter Kit'],
            ['angle' => "Bad logo briefs lead to 8 revision cycles and mutual frustration. I built a template that prevents that completely.", 'keyword' => 'BRIEF',    'resource' => 'Logo Design Brief Template'],
            ['angle' => "Most food brands lose distributor meetings before the second slide. Not because the product is bad. Because the deck tells the wrong story.", 'keyword' => 'PITCH',    'resource' => 'FMCG Distributor Pitch Deck Template'],
            ['angle' => "Investors don't fund products. They fund stories with evidence. Here's how to structure that narrative for a consumer brand pitch.", 'keyword' => 'INVEST',   'resource' => 'Investor Pitch Narrative Guide'],
            ['angle' => "The copy on most food packaging is either a legal disclaimer or a cliché. I wrote a formula sheet that fixes this for every packaging panel.", 'keyword' => 'COPY',     'resource' => 'Packaging Copy Formula Sheet'],
            ['angle' => "Your brand sounds different on Instagram vs website vs packaging. One page fixes this permanently.", 'keyword' => 'VOICE',    'resource' => 'Brand Voice Style Guide Template'],
            ['angle' => "'Is a packaging redesign worth it?' Every founder asks this. I built a calculator that gives you a real answer based on your margins.", 'keyword' => 'ROI',      'resource' => 'Packaging Redesign ROI Calculator'],
            ['angle' => "Before you rebrand, you need to know what you're rebranding into. Here's how to audit your top 5 competitors properly.", 'keyword' => 'COMPETE',  'resource' => 'Competitor Brand Audit Template'],
            ['angle' => "Launching a new brand website without this checklist is how you go live with broken links, wrong fonts, and a homepage nobody understands.", 'keyword' => 'LAUNCH',   'resource' => 'Pre-Launch Website Checklist'],
            ['angle' => "4 weeks of LinkedIn content for agencies targeting food and FMCG brands — with post hooks, formats, and themes for each week.", 'keyword' => 'LI',       'resource' => 'LinkedIn Content Calendar for FMCG Agencies'],
            ['angle' => "Most food brand names are either too generic to be memorable or too clever to be understood. I built a naming framework for Indian food brands.", 'keyword' => 'NAME',     'resource' => 'Brand Naming Framework for Indian Food Brands'],
            ['angle' => "30+ copy examples from the best Indian food brand websites — adaptable, not copyable. Yours free.", 'keyword' => 'SWIPE',    'resource' => 'Website Copy Swipe File for Food Brands'],
        ];
    }

    /**
     * Get the correct angle for a given rotation index.
     * Pattern: every 3rd post (index % 3 == 2) is a lead magnet.
     */
    public static function getAngleForIndex(int $index): array
    {
        $isLeadMagnet = ($index % config('linkedin.lead_magnet_every', 3)) === (config('linkedin.lead_magnet_every', 3) - 1);

        if ($isLeadMagnet) {
            $magnets   = self::leadMagnetAngles();
            $magnet    = $magnets[$index % count($magnets)];
            return array_merge($magnet, ['post_type' => 'lead_magnet']);
        }

        $swipe  = self::swipeAngles();
        $angle  = $swipe[$index % count($swipe)];
        return array_merge($angle, ['post_type' => 'swipe', 'keyword' => null, 'resource' => null]);
    }
}
