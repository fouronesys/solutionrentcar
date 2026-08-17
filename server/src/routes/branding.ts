import { Router } from "express";
import { resolveCompany } from "../auth.js";
import { companyToArray, h, ok } from "../helpers.js";

/** Endpoint público de branding: la app resuelve la empresa por X-Company / ?company=. */
export const brandingRouter = Router();

brandingRouter.get("/", h(async (req, res) => {
  const company = await resolveCompany(req, res);
  if (!company) return;
  const c = companyToArray(req, company);
  return ok(res, {
    company: {
      id: c.id,
      slug: c.slug,
      name: c.name,
      logo: c.logo,
      colors: c.colors,
      color_primary: c.color_primary,
      color_secondary: c.color_secondary,
      currency: c.currency,
      phone: c.phone,
      email: c.email,
      address: c.address,
    },
  });
}));
